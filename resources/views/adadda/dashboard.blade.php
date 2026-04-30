@extends('layouts.dashboard', ['role' => 'admin', 'activeModule' => 'dashboard', 'title' => 'Dashboard', 'subtitle' => 'System Overview'])

@section('dashboard_content')
<div class="p-6 space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="/admin/schedule" class="block bg-white rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:border-[#f48545]/50 hover:bg-[#fff9f6] hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="p-4">
                <p class="text-sm font-medium text-gray-500">Total Sessions</p>
                <p class="text-2xl font-bold text-gray-900 mt-2 text-right">{{ $totalSessions > 0 ? $totalSessions : '0' }}</p>
                <p class="text-xs text-gray-400 mt-1 text-right">From counseling_sessions</p>
            </div>
        </a>
        <a href="/admin/schedule" class="block bg-white rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:border-[#f48545]/50 hover:bg-[#fff9f6] hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="p-4">
                <p class="text-sm font-medium text-gray-500">Pending Requests</p>
                <p class="text-2xl font-bold text-gray-900 mt-2 text-right">{{ $pendingRequestsCount > 0 ? $pendingRequestsCount : '0' }}</p>
                <p class="text-xs text-gray-400 mt-1 text-right">From session_requests</p>
            </div>
        </a>
        <a href="/admin/users" class="block bg-white rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:border-[#f48545]/50 hover:bg-[#fff9f6] hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="p-4">
                <p class="text-sm font-medium text-gray-500">Active Counselors</p>
                <p class="text-2xl font-bold text-gray-900 mt-2 text-right">{{ $activeCounselorsCount > 0 ? $activeCounselorsCount : '0' }}</p>
                <p class="text-xs text-gray-400 mt-1 text-right">From counselors table</p>
            </div>
        </a>
        <a href="/admin/users" class="block bg-white rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:border-[#f48545]/50 hover:bg-[#fff9f6] hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="p-4">
                <p class="text-sm font-medium text-gray-500">Students</p>
                <p class="text-2xl font-bold text-gray-900 mt-2 text-right">{{ $totalStudents > 0 ? $totalStudents : '0' }}</p>
                <p class="text-xs text-gray-400 mt-1 text-right">Total registered</p>
            </div>
        </a>
    </div>

    <!-- Recent Requests -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-row items-center justify-between">
            <h3 class="text-base font-semibold leading-none tracking-tight">Pending Requests</h3>
            <a href="/admin/schedule" class="text-sm font-medium text-[#f48545] hover:text-[#e67a3b] transition-colors">View All</a>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @forelse($recentRequests as $req)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 gap-3 bg-white border border-gray-100 rounded-xl hover:border-[#f48545]/30 hover:bg-[#fff9f6] transition-all shadow-sm shadow-gray-100/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#fef4ee] rounded-full flex items-center justify-center shrink-0">
                            <i data-lucide="user" class="w-5 h-5 text-[#f48545]"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $req->student->name ?? 'Unknown Student' }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $req->type }} - {{ $req->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-start sm:self-auto ml-13 sm:ml-0">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#fff1eb] text-[#d9733a]">Pending</span>
                        <a href="/admin/schedule" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-[#f48545]/20 bg-white hover:bg-[#fff9f6] text-[#d9733a] h-8 px-4 shadow-sm transition-colors cursor-pointer">Review</a>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-gray-500 text-sm">
                    No pending requests currently.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Counselor Status -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-row items-center justify-between">
            <h3 class="text-base font-semibold leading-none tracking-tight">Counselor Status</h3>
            <a href="/admin/users" class="text-sm font-medium text-[#f48545] hover:text-[#e67a3b] transition-colors">Manage</a>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($counselors as $counselor)
                <div class="flex items-center justify-between p-3 bg-white border border-gray-100 rounded-xl cursor-pointer hover:border-[#f48545]/50 hover:bg-[#fff9f6] transition-all shadow-sm shadow-gray-100/50">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-50 rounded-full flex items-center justify-center text-xs font-bold text-gray-600 border border-gray-100">
                            {{ substr($counselor->name, 0, 1) }}
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ $counselor->name }}</span>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full {{ $counselor->status == 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ $counselor->status ?? 'Active' }}
                    </span>
                </div>
                @empty
                <div class="col-span-2 text-center py-4 text-gray-500 text-sm">
                    No active counselors.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
