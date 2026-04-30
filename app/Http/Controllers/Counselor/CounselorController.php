<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CounselingSession;
use App\Models\AvailabilityRequest;

class CounselorController extends Controller
{
    public function dashboard()
    {
        $counselorId = Auth::guard('counselor')->id();
        
        $todaysSessions = CounselingSession::where('counselor_id', $counselorId)
            ->whereDate('date', today())
            ->where('status', '!=', 'cancelled')
            ->count();
            
        $thisWeek = CounselingSession::where('counselor_id', $counselorId)
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
            
        $completed = CounselingSession::where('counselor_id', $counselorId)
            ->where('status', 'Completed')
            ->count();

        $upcomingSessions = CounselingSession::with('student')
            ->where('counselor_id', $counselorId)
            ->where('date', '>=', today())
            ->where('status', 'assigned')
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->take(5)
            ->get();

        return view('counselor.dashboard', compact('todaysSessions', 'thisWeek', 'completed', 'upcomingSessions'));
    }

    public function sessions(Request $request)
    {
        $counselorId = Auth::guard('counselor')->id();
        $query = CounselingSession::with('student')->where('counselor_id', $counselorId);

        $filter = $request->query('filter', 'all');

        if ($filter === 'upcoming') {
            $query->where('date', '>=', today())->where('status', 'assigned');
        } elseif ($filter === 'completed') {
            $query->where('status', 'Completed');
        }

        $sessions = $query->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(10)->withQueryString();
            
        return view('counselor.sessions', compact('sessions', 'filter'));
    }

    public function availability()
    {
        $counselorId = Auth::guard('counselor')->id();
        $requests = AvailabilityRequest::where('counselor_id', $counselorId)->latest()->paginate(10);
        return view('counselor.availability', compact('requests'));
    }

    public function completeSession($id)
    {
        $session = CounselingSession::where('id', $id)
            ->where('counselor_id', Auth::guard('counselor')->id())
            ->firstOrFail();

        $session->update(['status' => 'Completed']);

        return redirect()->back()->with('success', 'Session marked as completed.');
    }

    public function storeAvailability(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'required|string'
        ]);

        AvailabilityRequest::create([
            'counselor_id' => Auth::guard('counselor')->id(),
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        return redirect('/counselor/availability')->with('success', 'Unavailability request submitted successfully.');
    }

    public function walkIn()
    {
        $counselorId = Auth::guard('counselor')->id();
        $counselor = \App\Models\Counselor::with(['counselingSessions' => function($q) {
            $q->where('status', 'assigned')->where('date', '>=', today());
        }, 'blockedTimes' => function($q) {
            $q->where('date', '>=', today());
        }])->findOrFail($counselorId);

        $globalBlockedTimes = \App\Models\BlockedTime::whereNull('counselor_id')
                                ->where('date', '>=', today())
                                ->get();

        return view('counselor.walkin', compact('counselor', 'globalBlockedTimes'));
    }

    public function storeWalkIn(Request $request)
    {
        $request->validate([
            'student_name' => 'required|string',
            'student_email' => 'required|email',
            'type' => 'required|string',
            'preferred_date' => 'required|date',
            'preferred_time' => 'required|date_format:H:i',
            'description' => 'required|string',
        ]);

        $student = \App\Models\Student::where('email', $request->student_email)->first();
        $isNew = false;
        
        if (!$student) {
            $student = \App\Models\Student::create([
                'email' => $request->student_email,
                'name' => $request->student_name,
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'status' => 'Active'
            ]);
            $isNew = true;
        }

        $counselorId = Auth::guard('counselor')->id();

        \App\Models\SessionRequest::create([
            'student_id' => $student->id,
            'counselor_id' => $counselorId,
            'type' => $request->type,
            'urgency' => 'High', // Walk-ins assume high urgency
            'preferred_date' => $request->preferred_date,
            'preferred_time' => $request->preferred_time,
            'description' => $request->description,
            'status' => 'pending',
            'is_walk_in' => true
        ]);

        $startTime = \Carbon\Carbon::parse($request->preferred_time)->format('H:i');
        $endTime = \Carbon\Carbon::parse($request->preferred_time)->addHour()->format('H:i');

        \App\Models\BlockedTime::create([
            'counselor_id' => $counselorId,
            'date' => $request->preferred_date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'reason' => 'Walk-in Pending Approval'
        ]);

        $msg = 'Walk-in session submitted for admin approval.';
        if ($isNew) {
            $msg .= ' A new student account was created. Username: ' . $student->name . ' | Password: password123';
        }

        return redirect()->back()->with('success', $msg);
    }

    public function generateReport()
    {
        $counselorId = Auth::guard('counselor')->id();
        $sessions = \App\Models\CounselingSession::with('student')
            ->where('counselor_id', $counselorId)
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();
            
        $generatedBy = Auth::guard('counselor')->user()->name;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.sessions_pdf', [
            'sessions' => $sessions,
            'generatedBy' => $generatedBy,
            'reportType' => 'Personal'
        ]);

        return $pdf->download('counselor_sessions_report.pdf');
    }
}
