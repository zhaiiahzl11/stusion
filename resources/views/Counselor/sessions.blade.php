@extends('layouts.dashboard', ['role' => 'counselor', 'activeModule' => 'sessions', 'title' => 'My Sessions', 'subtitle' => 'View and manage all your counseling sessions'])

@section('dashboard_content')
<div class="p-6 space-y-4">
    <!-- Filter Tabs & Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex flex-wrap items-center gap-2">
            <a href="/counselor/sessions?filter=all" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ ($filter ?? 'all') == 'all' ? 'bg-[#f48545] text-white shadow-sm' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">All</a>
            <a href="/counselor/sessions?filter=upcoming" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ ($filter ?? 'all') == 'upcoming' ? 'bg-[#f48545] text-white shadow-sm' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">Upcoming</a>
            <a href="/counselor/sessions?filter=completed" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ ($filter ?? 'all') == 'completed' ? 'bg-[#f48545] text-white shadow-sm' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">Completed</a>
        </div>
        
        <a href="/counselor/reports/sessions" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-[#f48545] text-white hover:bg-[#e67a3b] shadow-sm h-10 px-4 transition-colors">
            <i data-lucide="download" class="w-4 h-4 mr-2"></i> Download My Report
        </a>
    </div>

    <!-- Sessions List -->
    <div class="space-y-4">
        @forelse($sessions as $session)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:border-[#f48545]/30 hover:shadow-md transition-all">
            <div class="p-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 {{ $session->status == 'Completed' ? 'bg-gray-50 shadow-sm shadow-gray-100' : 'bg-[#fef4ee] shadow-sm shadow-[#f48545]/10' }} rounded-full flex items-center justify-center">
                            <i data-lucide="graduation-cap" class="w-6 h-6 {{ $session->status == 'Completed' ? 'text-gray-400' : 'text-[#f48545]' }}"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $session->student->name ?? 'Unknown Student' }}</p>
                            <p class="text-sm font-medium text-gray-500">{{ $session->type }}</p>
                            <p class="text-xs font-medium text-gray-400 mt-1">{{ \Carbon\Carbon::parse($session->date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($session->time)->format('g:i A') }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto ml-16 sm:ml-0">
                        @if($session->status == 'Completed')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border bg-gray-50 text-gray-500 border-gray-200">Completed</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border bg-[#fff1eb] text-[#d9733a] border-[#fef4ee] shadow-sm">Assigned</span>
                        @endif
                        
                        <button onclick="openDetailsModal('{{ addslashes($session->student->name ?? 'Unknown Student') }}', '{{ addslashes($session->type) }}', '{{ \Carbon\Carbon::parse($session->date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($session->time)->format('g:i A') }}', '{{ $session->status }}')" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-gray-200 bg-white hover:bg-gray-50 h-8 px-4 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Details
                        </button>
                        
                        @if($session->status != 'Completed')
                        <form action="/counselor/sessions/{{ $session->id }}/complete" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-[#f48545] text-white hover:bg-[#e67a3b] shadow-sm h-8 px-4 transition-colors">
                                <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> Complete
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-4 text-gray-500 text-sm">
            You do not have any counseling sessions currently assigned to you.
        </div>
        @endforelse
    </div>
    <div class="mt-4">
        {{ $sessions->links() }}
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
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
