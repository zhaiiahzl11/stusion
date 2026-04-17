@extends('layouts.dashboard', ['role' => 'admin', 'activeModule' => 'schedule', 'title' => 'Schedule Control', 'subtitle' => 'Manage session assignments and availability'])

@section('dashboard_content')
<div class="p-6 space-y-6">

    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 text-sm font-bold text-emerald-800 border border-emerald-100 shadow-sm">
        {{ session('success') }}
    </div>
    @endif
    
    @if ($errors->any())
    <div class="p-4 rounded-xl bg-red-50 text-sm font-bold text-red-800 border border-red-100 shadow-sm">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Availability Requests -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold leading-none tracking-tight">Counselor Availability Requests</h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @forelse($availabilityRequests as $request)
                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-xl hover:border-[#f48545]/30 hover:bg-[#fff9f6] transition-colors shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                            <i data-lucide="user" class="w-5 h-5 text-gray-500"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $request->counselor->name ?? 'Unknown Counselor' }}</p>
                            <p class="text-sm text-gray-500">{{ $request->days }} - {{ \Carbon\Carbon::parse($request->start_time)->format('gA') }} to {{ \Carbon\Carbon::parse($request->end_time)->format('gA') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border bg-[#fff1eb] text-[#d9733a] border-[#fef4ee] shadow-sm">Pending</span>
                        <form action="/admin/schedule/availability/{{ $request->id }}/approve" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex flex-row items-center justify-center rounded-md text-sm font-medium border border-emerald-200 text-emerald-600 hover:bg-emerald-50 h-8 px-3">
                                <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> Approve
                            </button>
                        </form>
                        <form action="/admin/schedule/availability/{{ $request->id }}/reject" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex flex-row items-center justify-center rounded-md text-sm font-medium border border-red-200 text-red-600 hover:bg-red-50 h-8 px-3">
                                <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i> Reject
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-gray-500 text-sm">
                    No pending availability requests.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pending Assignments -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold leading-none tracking-tight">Pending Session Assignments</h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @forelse($pendingSessionRequests as $sessReq)
                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-xl hover:border-[#f48545]/30 hover:bg-[#fff9f6] transition-colors shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-[#fef4ee] rounded-full flex items-center justify-center">
                            <i data-lucide="graduation-cap" class="w-5 h-5 text-[#f48545]"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $sessReq->student->name ?? 'Unknown Student' }}</p>
                            <p class="text-sm text-gray-500">{{ $sessReq->type }} - Requested {{ $sessReq->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs px-2.5 py-1 font-medium rounded-full {{ $sessReq->urgency == 'High' ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-[#fff1eb] text-[#d9733a] border border-[#fef4ee]' }}">{{ $sessReq->urgency }} Priority</span>
                        <button onclick="openAssignModal({{ $sessReq->id }})" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-[#f48545] text-white hover:bg-[#e67a3b] h-8 px-4 transition-colors shadow-sm">Assign Counselor</button>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-gray-500 text-sm">
                    No pending session assignments across all students.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Assign Counselor Modal Overlay -->
<div id="assignModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all translate-y-4 sm:translate-y-0 sm:scale-95">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-base font-bold text-gray-900">Assign Counselor</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form action="/admin/schedule/assign" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="session_request_id" id="session_request_id" value="">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Counselor</label>
                <select name="counselor_id" required class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
                    <option value="" disabled selected>Select from active counselors</option>
                    @foreach($counselors as $counselor)
                        <option value="{{ $counselor->id }}">{{ $counselor->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-4 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#f48545] rounded-lg hover:bg-[#e67a3b] shadow-sm transition-colors">Confirm Assignment</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAssignModal(requestId) {
        document.getElementById('session_request_id').value = requestId;
        const modal = document.getElementById('assignModal');
        const modalContent = modal.querySelector('div.transform');
        
        modal.classList.remove('hidden');
        void modal.offsetWidth;
        modalContent.classList.remove('translate-y-4', 'sm:scale-95');
        modalContent.classList.add('translate-y-0', 'sm:scale-100');
    }

    function closeModal() {
        const modal = document.getElementById('assignModal');
        const modalContent = modal.querySelector('div.transform');
        
        modalContent.classList.remove('translate-y-0', 'sm:scale-100');
        modalContent.classList.add('translate-y-4', 'sm:scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }
</script>
@endsection
