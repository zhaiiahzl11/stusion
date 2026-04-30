@extends('layouts.dashboard', ['role' => 'counselor', 'activeModule' => 'availability', 'title' => 'Unavailability Request', 'subtitle' => 'Request leave or time off'])

@section('dashboard_content')
<div class="p-6 space-y-6">
    <!-- Info Banner -->
    <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-lg">
        <i data-lucide="alert-circle" class="w-5 h-5 text-amber-600 mt-0.5"></i>
        <div>
            <p class="font-medium text-amber-800">Unavailability Requests</p>
            <p class="text-sm text-amber-700 mt-1">
                Use this form to request time off (e.g. emergencies, leaves). You cannot set your regular availability. Once approved, the requested time will be blocked.
            </p>
        </div>
    </div>

    <!-- Submit Request Button -->
    <button onclick="toggleModal('availabilityModal')" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-[#f48545] text-white hover:bg-[#e67a3b] shadow-sm transition-colors h-10 px-6 py-2">
        <i data-lucide="clock" class="w-4 h-4 mr-2"></i> Submit Leave Request
    </button>

    <!-- Current Requests -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mt-8">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold leading-none tracking-tight">My Leave Requests</h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @forelse($requests as $req)
                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-xl hover:border-[#f48545]/30 hover:shadow-md hover:bg-[#fff9f6] transition-all">
                    <div>
                        <p class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($req->date)->format('M d, Y') }}</p>
                        <p class="text-sm font-medium text-gray-500">{{ \Carbon\Carbon::parse($req->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($req->end_time)->format('g:i A') }}</p>
                        <p class="text-xs font-medium text-gray-400 mt-1">Submitted: {{ $req->created_at->format('M d') }}</p>
                    </div>
                    @if($req->status == 'Approved')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border bg-emerald-50 text-emerald-700 border-emerald-100 shadow-sm">Approved</span>
                    @elseif($req->status == 'pending')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border bg-[#fff1eb] text-[#d9733a] border-[#fef4ee] shadow-sm">Pending</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border bg-red-50 text-red-700 border-red-100 shadow-sm">Rejected</span>
                    @endif
                </div>
                @empty
                <div class="text-center py-4 text-gray-500 text-sm">
                    You have not submitted any leave requests.
                </div>
                @endforelse
            </div>
            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Overlay -->
<div id="availabilityModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all translate-y-4 sm:translate-y-0 sm:scale-95">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-base font-bold text-gray-900">Request Leave / Unavailability</h3>
            <button onclick="toggleModal('availabilityModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form action="/counselor/availability" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" required min="{{ date('Y-m-d') }}" class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                    <select name="start_time" required class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
                        <option value="" disabled selected>Select</option>
                        @for($i=8; $i<=17; $i++)
                            @php $timeVal = sprintf('%02d:00', $i); $display = date('g:i A', strtotime($timeVal)); @endphp
                            <option value="{{ $timeVal }}">{{ $display }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                    <select name="end_time" required class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
                        <option value="" disabled selected>Select</option>
                        @for($i=8; $i<=17; $i++)
                            @php $timeVal = sprintf('%02d:00', $i); $display = date('g:i A', strtotime($timeVal)); @endphp
                            <option value="{{ $timeVal }}">{{ $display }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason / Note</label>
                <textarea name="notes" required placeholder="Reason for unavailability..." class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545] min-h-[80px]"></textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-2">
                <button type="button" onclick="toggleModal('availabilityModal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#f48545] rounded-lg hover:bg-[#e67a3b] shadow-sm transition-colors">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        const modalContent = modal.querySelector('div.transform');
        
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            // Trigger reflow
            void modal.offsetWidth;
            modalContent.classList.remove('translate-y-4', 'sm:scale-95');
            modalContent.classList.add('translate-y-0', 'sm:scale-100');
        } else {
            modalContent.classList.remove('translate-y-0', 'sm:scale-100');
            modalContent.classList.add('translate-y-4', 'sm:scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }
    }
</script>
@endsection
