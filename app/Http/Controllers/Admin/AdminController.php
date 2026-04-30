<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\SessionRequest;
use App\Models\Counselor;
use App\Models\CounselingSession;
use App\Models\Student;
use App\Models\Admin;
use App\Models\AvailabilityRequest;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalSessions = CounselingSession::count();
        $pendingRequestsCount = SessionRequest::where('status', 'pending')->count();
        $activeCounselorsCount = Counselor::count();
        $totalStudents = Student::count();

        $recentRequests = SessionRequest::with('student')->where('status', 'pending')->latest()->take(5)->get();
        $counselors = Counselor::all();

        return view('Admin.dashboard', compact('totalSessions', 'pendingRequestsCount', 'activeCounselorsCount', 'totalStudents', 'recentRequests', 'counselors'));
    }

    public function users(Request $request)
    {
        $filter = $request->query('role', 'All');
        $users = collect();

        if ($filter == 'All' || $filter == 'Admin') {
            foreach(Admin::all() as $admin) { $admin->role = 'Admin'; $users->push($admin); }
        }
        if ($filter == 'All' || $filter == 'Counselor') {
            foreach(Counselor::all() as $counselor) { $counselor->role = 'Counselor'; $users->push($counselor); }
        }
        if ($filter == 'All' || $filter == 'Student') {
            foreach(Student::all() as $student) { $student->role = 'Student'; $users->push($student); }
        }

        $perPage = 10;
        $page = $request->input('page', 1);
        $paginatedUsers = new LengthAwarePaginator(
            $users->forPage($page, $perPage),
            $users->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $users = $paginatedUsers;

        return view('admin.users', compact('users', 'filter'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:admins,email|unique:counselors,email|unique:students,email',
            'role' => 'required|in:Admin,Counselor,Student'
        ]);

        $password = Hash::make('password123');
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password
        ];

        if ($request->role === 'Admin') {
            Admin::create($data);
        } elseif ($request->role === 'Counselor') {
            Counselor::create($data);
        } elseif ($request->role === 'Student') {
            Student::create($data);
        }

        return redirect()->back()->with('success', 'User added successfully with default password: password123');
    }

    public function destroyUser($role, $id)
    {
        if ($role === 'Admin' && Admin::count() > 1) {
            Admin::findOrFail($id)->delete();
        } elseif ($role === 'Counselor') {
            Counselor::findOrFail($id)->delete();
        } elseif ($role === 'Student') {
            Student::findOrFail($id)->delete();
        }
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function updateUser(Request $request, $role, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'nullable|min:6'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($role === 'Admin') {
            Admin::findOrFail($id)->update($data);
        } elseif ($role === 'Counselor') {
            Counselor::findOrFail($id)->update($data);
        } elseif ($role === 'Student') {
            Student::findOrFail($id)->update($data);
        }

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function schedule()
    {
        $availabilityRequests = AvailabilityRequest::with('counselor')->where('status', 'pending')->latest()->paginate(10, ['*'], 'availability_page')->withQueryString();
        $pendingSessionRequests = SessionRequest::with('student', 'counselor')->where('status', 'pending')->latest()->paginate(10, ['*'], 'sessions_page')->withQueryString();
        $counselors = Counselor::all();
        $blockedTimes = \App\Models\BlockedTime::with('counselor')->orderBy('date', 'desc')->paginate(10, ['*'], 'blocked_page')->withQueryString();

        return view('admin.schedule', compact('availabilityRequests', 'pendingSessionRequests', 'counselors', 'blockedTimes'));
    }

    public function approveAvailability($id)
    {
        $request = AvailabilityRequest::findOrFail($id);
        $request->update(['status' => 'Approved']);

        \App\Models\BlockedTime::create([
            'counselor_id' => $request->counselor_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->notes ?? 'Approved Leave'
        ]);

        return redirect()->back()->with('success', 'Leave request approved and schedule blocked.');
    }

    public function rejectAvailability($id)
    {
        AvailabilityRequest::findOrFail($id)->update(['status' => 'Rejected']);
        return redirect()->back()->with('success', 'Availability rejected.');
    }

    public function assignCounselor(Request $request)
    {
        $request->validate([
            'session_request_id' => 'required|exists:session_requests,id',
            'counselor_id' => 'required|exists:counselors,id'
        ]);

        $sessionReq = SessionRequest::findOrFail($request->session_request_id);
        
        $date = $sessionReq->preferred_date ?? today()->toDateString();
        if (\Carbon\Carbon::parse($date)->isWeekend()) {
            return redirect()->back()->with('error', 'Saturdays and Sundays cannot be scheduled.');
        }

        $sessionReq->update(['status' => 'assigned']);

        CounselingSession::create([
            'student_id' => $sessionReq->student_id,
            'counselor_id' => $request->counselor_id,
            'date' => $date,
            'time' => $sessionReq->preferred_time ?? '09:00:00',
            'type' => $sessionReq->type,
            'status' => 'assigned'
        ]);

        return redirect()->back()->with('success', 'Counselor assigned effectively.');
    }

    public function approveWalkIn($id)
    {
        $sessionReq = SessionRequest::where('is_walk_in', true)->findOrFail($id);

        $sessionReq->update(['status' => 'assigned']);

        CounselingSession::create([
            'student_id' => $sessionReq->student_id,
            'counselor_id' => $sessionReq->counselor_id,
            'date' => $sessionReq->preferred_date,
            'time' => $sessionReq->preferred_time,
            'type' => $sessionReq->type,
            'status' => 'assigned'
        ]);

        $startTime = \Carbon\Carbon::parse($sessionReq->preferred_time)->format('H:i');
        \App\Models\BlockedTime::where('counselor_id', $sessionReq->counselor_id)
            ->where('date', $sessionReq->preferred_date)
            ->where('start_time', 'like', $startTime.'%')
            ->where('reason', 'Walk-in Pending Approval')
            ->delete();

        return redirect()->back()->with('success', 'Walk-in session approved and finalized.');
    }

    public function storeBlockedTime(Request $request)
    {
        $request->validate([
            'counselor_id' => 'nullable|exists:counselors,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'nullable|string'
        ]);

        \App\Models\BlockedTime::create($request->only('counselor_id', 'date', 'start_time', 'end_time', 'reason'));

        return redirect()->back()->with('success', 'Time blocked successfully.');
    }

    public function deleteBlockedTime($id)
    {
        \App\Models\BlockedTime::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Blocked time removed.');
    }

    public function generateReport()
    {
        $sessions = CounselingSession::with(['student', 'counselor'])->orderBy('date', 'desc')->orderBy('time', 'desc')->get();
        $generatedBy = \Illuminate\Support\Facades\Auth::guard('admin')->user()->name;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.sessions_pdf', [
            'sessions' => $sessions,
            'generatedBy' => $generatedBy,
            'reportType' => 'Comprehensive'
        ]);

        return $pdf->download('admin_sessions_report.pdf');
    }
}
