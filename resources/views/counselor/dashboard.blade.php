@extends('layouts.dashboard', ['role' => 'counselor', 'activeModule' => 'dashboard', 'title' => 'Counselor Dashboard', 'subtitle' => 'Manage your sessions and availability'])

@section('dashboard_content')
<div class="p-6 space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="/counselor/sessions" class="block bg-white rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:border-[#f48545]/50 hover:bg-[#fff9f6] hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="p-4">
                <p class="text-sm font-medium text-gray-500">Today's Sessions</p>
                <p class="text-2xl font-bold text-gray-900 mt-2 text-right">{{ $todaysSessions > 0 ? $todaysSessions : '0' }}</p>
            </div>
        </a>
        <a href="/counselor/sessions" class="block bg-white rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:border-[#f48545]/50 hover:bg-[#fff9f6] hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="p-4">
                <p class="text-sm font-medium text-gray-500">This Week</p>
                <p class="text-2xl font-bold text-gray-900 mt-2 text-right">{{ $thisWeek > 0 ? $thisWeek : '0' }}</p>
            </div>
        </a>
        <a href="/counselor/sessions" class="block bg-white rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:border-[#f48545]/50 hover:bg-[#fff9f6] hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="p-4">
                <p class="text-sm font-medium text-gray-500">Completed</p>
                <p class="text-2xl font-bold text-gray-900 mt-2 text-right">{{ $completed > 0 ? $completed : '0' }}</p>
            </div>
        </a>
    </div>

    <!-- Upcoming Sessions -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-row items-center justify-between">
            <h3 class="text-base font-semibold leading-none tracking-tight">Upcoming Sessions</h3>
            <a href="/counselor/sessions" class="text-sm font-medium text-[#f48545] hover:text-[#e67a3b] transition-colors">View All</a>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @forelse($upcomingSessions as $session)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 border border-gray-100 rounded-xl hover:border-[#f48545]/30 hover:bg-[#fff9f6] transition-all shadow-sm shadow-gray-100/50 gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#fef4ee] rounded-lg flex flex-col items-center justify-center border border-[#f48545]/10 shadow-sm shrink-0">
                            @if(\Carbon\Carbon::parse($session->date)->isToday())
                                <span class="text-[10px] text-[#f48545] font-bold tracking-wider">TODAY</span>
                            @else
                                <span class="text-xs text-[#f48545] font-bold">{{ \Carbon\Carbon::parse($session->date)->format('M d') }}</span>
                            @endif
                            <span class="text-xs text-[#d9733a] font-medium">{{ \Carbon\Carbon::parse($session->time)->format('g:i') }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $session->student->name ?? 'Unknown Student' }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $session->type }} - {{ \Carbon\Carbon::parse($session->time)->format('g:i A') }}</p>
                        </div>
                    </div>
                    <button onclick="openDetailsModal('{{ addslashes($session->student->name ?? 'Unknown Student') }}', '{{ addslashes($session->type) }}', '{{ \Carbon\Carbon::parse($session->date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($session->time)->format('g:i A') }}', '{{ $session->status }}')" class="self-start sm:self-auto ml-16 sm:ml-0 inline-flex items-center justify-center rounded-md text-sm font-medium border border-gray-200 bg-white hover:bg-gray-50 h-8 px-4 shadow-sm transition-colors">View Details</button>
                </div>
                @empty
                <div class="text-center py-4 text-gray-500 text-sm">
                    No upcoming sessions scheduled.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Notifications -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold leading-none tracking-tight">Recent Notifications</h3>
        </div>
        <div class="p-6">
            <div class="space-y-2">
                @php
                    $alerts = collect();
                    $counselorId = \Illuminate\Support\Facades\Auth::guard('counselor')->id();
                    
                    $todaySessionsCount = \App\Models\CounselingSession::where('counselor_id', $counselorId)
                        ->whereDate('date', today())
                        ->where('status', 'assigned')
                        ->count();
                        
                    if ($todaySessionsCount > 0) {
                        $alerts->push([
                            'title' => 'Today\'s Sessions',
                            'desc' => 'You have ' . $todaySessionsCount . ' session(s) scheduled for today.',
                            'time' => 'Action required',
                            'color' => 'bg-[#f48545]',
                            'border' => 'border-[#f48545]/20'
                        ]);
                    }
                    
                    $upcomingSessionsCount = \App\Models\CounselingSession::where('counselor_id', $counselorId)
                        ->whereDate('date', '>', today())
                        ->where('status', 'assigned')
                        ->count();
                        
                    if ($upcomingSessionsCount > 0) {
                        $alerts->push([
                            'title' => 'Upcoming Sessions',
                            'desc' => 'You have ' . $upcomingSessionsCount . ' upcoming session(s).',
                            'time' => 'Upcoming',
                            'color' => 'bg-emerald-500',
                            'border' => 'border-emerald-500/20'
                        ]);
                    }
                @endphp

                @forelse($alerts as $alert)
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-white hover:shadow-sm hover:border hover:{{ $alert['border'] }} border border-transparent transition-all">
                    <div class="w-2 h-2 rounded-full {{ $alert['color'] }} shadow-sm"></div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">{{ $alert['title'] }}</p>
                        <p class="text-sm text-gray-600">{{ $alert['desc'] }}</p>
                    </div>
                    <span class="text-xs text-gray-500">{{ $alert['time'] }}</span>
                </div>
                @empty
                <div class="text-center py-4 text-gray-500 text-sm">
                    You're all caught up! No recent notifications.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Details Modal Overlay -->
<div id="detailsModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all translate-y-4 sm:translate-y-0 sm:scale-95">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-base font-bold text-gray-900">Session Details</h3>
            <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="p-6 space-y-4">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Student</p>
                <p id="modal_student" class="text-base font-bold text-gray-900"></p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Session Type</p>
                    <p id="modal_type" class="text-sm font-semibold text-gray-900"></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Status</p>
                    <p id="modal_status" class="text-sm font-semibold text-[#f48545] capitalize"></p>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Schedule Info</p>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                    <p id="modal_datetime" class="text-sm font-semibold text-gray-900"></p>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="button" onclick="closeDetailsModal()" class="px-6 py-2 text-sm font-medium text-white bg-[#f48545] rounded-lg hover:bg-[#e67a3b] shadow-sm transition-colors">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openDetailsModal(student, type, datetime, status) {
        document.getElementById('modal_student').textContent = student;
        document.getElementById('modal_type').textContent = type;
        document.getElementById('modal_datetime').textContent = datetime;
        document.getElementById('modal_status').textContent = status;
        
        const modal = document.getElementById('detailsModal');
        const modalContent = modal.querySelector('div.transform');
        
        modal.classList.remove('hidden');
        void modal.offsetWidth;
        modalContent.classList.remove('translate-y-4', 'sm:scale-95');
        modalContent.classList.add('translate-y-0', 'sm:scale-100');
    }

    function closeDetailsModal() {
        const modal = document.getElementById('detailsModal');
        const modalContent = modal.querySelector('div.transform');
        
        modalContent.classList.remove('translate-y-0', 'sm:scale-100');
        modalContent.classList.add('translate-y-4', 'sm:scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }
</script>
@endsection
