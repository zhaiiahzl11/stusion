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
            ->get();
            
        return view('counselor.sessions', compact('sessions', 'filter'));
    }

    public function availability()
    {
        $counselorId = Auth::guard('counselor')->id();
        $requests = AvailabilityRequest::where('counselor_id', $counselorId)->latest()->get();
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
            'days' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required'
        ]);

        AvailabilityRequest::create([
            'counselor_id' => Auth::guard('counselor')->id(),
            'days' => $request->days,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'pending'
        ]);

        return redirect('/counselor/availability')->with('success', 'Availability request submitted successfully.');
    }
}
