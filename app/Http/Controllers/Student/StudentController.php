<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CounselingSession;
use App\Models\SessionRequest;

class StudentController extends Controller
{
    public function dashboard()
    {
        $studentId = Auth::guard('student')->id();

        $upcoming = CounselingSession::where('student_id', $studentId)
            ->where('date', '>=', today())
            ->where('status', 'assigned')
            ->count();
            
        $pending = SessionRequest::where('student_id', $studentId)
            ->where('status', 'pending')
            ->count();
            
        $completed = CounselingSession::where('student_id', $studentId)
            ->where('status', 'Completed')
            ->count();
            
        $nextSession = CounselingSession::with('counselor')
            ->where('student_id', $studentId)
            ->where('date', '>=', today())
            ->where('status', 'assigned')
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->first();

        return view('student.dashboard', compact('upcoming', 'pending', 'completed', 'nextSession'));
    }

    public function requestSession()
    {
        $counselors = \App\Models\Counselor::with(['counselingSessions' => function($q) {
            $q->where('status', 'assigned')->where('date', '>=', today());
        }, 'blockedTimes' => function($q) {
            $q->where('date', '>=', today());
        }])->get();

        $globalBlockedTimes = \App\Models\BlockedTime::whereNull('counselor_id')
                                ->where('date', '>=', today())
                                ->get();

        return view('student.request', compact('counselors', 'globalBlockedTimes'));
    }

    public function storeRequest(Request $request)
    {
        $request->validate([
            'counselor_id' => 'required|exists:counselors,id',
            'type' => 'required|string',
            'urgency' => 'required|string|in:Low,Medium,High',
            'preferred_date' => 'required|date',
            'preferred_time' => 'required|date_format:H:i',
            'description' => 'required|string',
        ]);

        if (\Carbon\Carbon::parse($request->preferred_date)->isWeekend()) {
            return redirect()->back()->withErrors(['preferred_date' => 'Saturdays and Sundays are not available.'])->withInput();
        }

        SessionRequest::create([
            'student_id' => Auth::guard('student')->id(),
            'counselor_id' => $request->counselor_id,
            'type' => $request->type,
            'urgency' => $request->urgency,
            'preferred_date' => $request->preferred_date,
            'preferred_time' => $request->preferred_time,
            'description' => $request->description,
            'status' => 'pending'
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Session request submitted successfully!');
    }

    public function sessions()
    {
        $studentId = Auth::guard('student')->id();
        $sessions = CounselingSession::with('counselor')
            ->where('student_id', $studentId)
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();
            
        $pendingRequests = SessionRequest::where('student_id', $studentId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('student.sessions', compact('sessions', 'pendingRequests'));
    }
}
