@extends('layouts.dashboard', ['role' => 'student', 'activeModule' => 'dashboard', 'title' => 'Student Dashboard', 'subtitle' => 'System Overview'])

@section('dashboard_content')
<div class="p-6 space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-[#fff9f6] border border-[#f48545]/20 rounded-xl shadow-sm">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900">Welcome back, {{ Auth::guard('student')->user()->name }}!</h2>
            @if($nextSession)
                <p class="text-gray-500 mt-1">Your next session is scheduled for {{ \Carbon\Carbon::parse($nextSession->date)->format('l, M d') }} at {{ \Carbon\Carbon::parse($nextSession->time)->format('g:i A') }}</p>
                <button class="mt-4 inline-flex items-center justify-center rounded-md text-sm font-medium bg-[#f48545] text-white hover:bg-[#e67a3b] shadow-sm transition-colors h-10 px-6 py-2">
                    View Session Details
                </button>
            @else
                <p class="text-gray-500 mt-1">You don't have any upcoming sessions scheduled.</p>
                <a href="/student/request" class="mt-4 inline-flex items-center justify-center rounded-md text-sm font-medium bg-[#f48545] text-white hover:bg-[#e67a3b] shadow-sm transition-colors h-10 px-6 py-2">
                    Request a Session
                </a>
            @endif
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:border-[#f48545]/50 hover:bg-[#fff9f6] hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="p-4 text-center">
                <div class="w-12 h-12 bg-[#fef4ee] border border-[#f48545]/20 rounded-full flex items-center justify-center mx-auto">
                    <i data-lucide="clock" class="w-6 h-6 text-[#f48545]"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-3">{{ $upcoming > 0 ? $upcoming : '0' }}</p>
                <p class="text-sm font-medium text-gray-500 mt-1">Upcoming</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:border-[#f48545]/50 hover:bg-[#fff9f6] hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="p-4 text-center">
                <div class="w-12 h-12 bg-[#fef4ee] border border-[#f48545]/20 rounded-full flex items-center justify-center mx-auto">
                    <i data-lucide="alert-circle" class="w-6 h-6 text-[#f48545]"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-3">{{ $pending > 0 ? $pending : '0' }}</p>
                <p class="text-sm font-medium text-gray-500 mt-1">Pending</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:border-[#f48545]/50 hover:bg-[#fff9f6] hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="p-4 text-center">
                <div class="w-12 h-12 bg-emerald-50 border border-emerald-100 rounded-full flex items-center justify-center mx-auto">
                    <i data-lucide="check-circle" class="w-6 h-6 text-emerald-500"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-3">{{ $completed > 0 ? $completed : '0' }}</p>
                <p class="text-sm font-medium text-gray-500 mt-1">Completed</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold leading-none tracking-tight">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-3">
                <a href="/student/request" class="inline-flex flex-col items-center justify-center rounded-md text-sm font-medium border border-gray-300 bg-white hover:bg-gray-100 h-auto py-4">
                    <i data-lucide="send" class="w-5 h-5 mb-2"></i>
                    <span>Request Session</span>
                </a>
                <a href="/student/sessions" class="inline-flex flex-col items-center justify-center rounded-md text-sm font-medium border border-gray-300 bg-white hover:bg-gray-100 h-auto py-4">
                    <i data-lucide="file-text" class="w-5 h-5 mb-2"></i>
                    <span>View Sessions</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Announcements -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold leading-none tracking-tight">Announcements</h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <div class="p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                    <p class="font-medium text-gray-900">New counseling services available</p>
                    <p class="text-xs text-gray-500 mt-1">Apr 3, 2025</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                    <p class="font-medium text-gray-900">Office hours extended for exam period</p>
                    <p class="text-xs text-gray-500 mt-1">Apr 1, 2025</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
