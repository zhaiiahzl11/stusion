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

        return view('admin.dashboard', compact('totalSessions', 'pendingRequestsCount', 'activeCounselorsCount', 'totalStudents', 'recentRequests', 'counselors'));
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
        $availabilityRequests = AvailabilityRequest::with('counselor')->where('status', 'pending')->latest()->get();
        $pendingSessionRequests = SessionRequest::with('student')->where('status', 'pending')->latest()->get();
        $counselors = Counselor::all();

        return view('admin.schedule', compact('availabilityRequests', 'pendingSessionRequests', 'counselors'));
    }

    public function approveAvailability($id)
    {
        AvailabilityRequest::findOrFail($id)->update(['status' => 'Approved']);
        return redirect()->back()->with('success', 'Availability approved.');
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
        $sessionReq->update(['status' => 'assigned']);

        CounselingSession::create([
            'student_id' => $sessionReq->student_id,
            'counselor_id' => $request->counselor_id,
            'date' => $sessionReq->preferred_date ?? today(),
            'time' => $sessionReq->preferred_time ?? '09:00:00',
            'type' => $sessionReq->type,
            'status' => 'assigned'
        ]);

        return redirect()->back()->with('success', 'Counselor assigned effectively.');
    }
}
