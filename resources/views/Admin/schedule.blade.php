@extends('layouts.dashboard', ['role' => 'admin', 'activeModule' => 'schedule', 'title' => 'Schedule Control', 'subtitle' => 'Manage session assignments and availability'])

@section('dashboard_content')
<div class="p-6 space-y-6">
    <div class="flex justify-end mb-2">
        <a href="/admin/reports/sessions" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-[#f48545] text-white hover:bg-[#e67a3b] shadow-sm h-10 px-4 transition-colors">
            <i data-lucide="download" class="w-4 h-4 mr-2"></i> Download All Sessions Report
        </a>
    </div>

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
            <h3 class="text-base font-semibold leading-none tracking-tight">Counselor Leave Requests</h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @forelse($availabilityRequests as $request)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 border border-gray-100 rounded-xl hover:border-[#f48545]/30 hover:bg-[#fff9f6] transition-colors shadow-sm gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center shrink-0">
                            <i data-lucide="user" class="w-5 h-5 text-gray-500"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $request->counselor->name ?? 'Unknown Counselor' }}</p>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($request->date)->format('M d, Y') }} | {{ \Carbon\Carbon::parse($request->start_time)->format('g:i A') }} to {{ \Carbon\Carbon::parse($request->end_time)->format('g:i A') }}</p>
                            @if($request->notes)
                                <p class="text-xs text-gray-500 mt-1 italic">Note: "{{ $request->notes }}"</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-start sm:self-auto ml-14 sm:ml-0">
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

    <!-- Blocked Times Management -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mt-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-base font-semibold leading-none tracking-tight">Manage Blocked Times</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.schedule.block.store') }}" method="POST" class="flex flex-col md:flex-row flex-wrap items-start md:items-end gap-4 mb-6 pb-6 border-b border-gray-100">
                @csrf
                <div class="w-full md:flex-1 md:min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Counselor (Optional)</label>
                    <select name="counselor_id" class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
                        <option value="">All Counselors (Global Block)</option>
                        @foreach($counselors as $counselor)
                            <option value="{{ $counselor->id }}">{{ $counselor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-1/2 md:w-36">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" required min="{{ date('Y-m-d') }}" class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
                </div>
                <div class="w-full sm:w-[calc(50%-1rem)] md:w-32">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                    <select name="start_time" required class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
                        <option value="" disabled selected>Select</option>
                        @for($i=8; $i<=17; $i++)
                            @php $timeVal = sprintf('%02d:00', $i); $display = date('g:i A', strtotime($timeVal)); @endphp
                            <option value="{{ $timeVal }}">{{ $display }}</option>
                        @endfor
                    </select>
                </div>
                <div class="w-full sm:w-[calc(50%-1rem)] md:w-32">
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                    <select name="end_time" required class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
                        <option value="" disabled selected>Select</option>
                        @for($i=8; $i<=17; $i++)
                            @php $timeVal = sprintf('%02d:00', $i); $display = date('g:i A', strtotime($timeVal)); @endphp
                            <option value="{{ $timeVal }}">{{ $display }}</option>
                        @endfor
                    </select>
                </div>
                <div class="w-full md:flex-1 md:min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                    <input type="text" name="reason" placeholder="e.g. Holiday" class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
                </div>
                <div class="w-full md:w-auto mt-2 md:mt-0">
                    <button type="submit" class="w-full h-10 px-6 flex items-center justify-center rounded-lg text-sm font-medium bg-[#f48545] text-white hover:bg-[#e67a3b] transition-colors shadow-sm">
                        Block Time
                    </button>
                </div>
            </form>

            <div class="space-y-3">
                @forelse($blockedTimes as $block)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 border border-gray-100 rounded-xl hover:border-[#f48545]/30 hover:bg-[#fff9f6] transition-colors shadow-sm gap-3">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 {{ $block->counselor_id ? 'bg-blue-50' : 'bg-red-50' }} rounded-full flex items-center justify-center shrink-0">
                            <i data-lucide="{{ $block->counselor_id ? 'user' : 'globe' }}" class="w-5 h-5 {{ $block->counselor_id ? 'text-blue-500' : 'text-red-500' }}"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate">
                                {{ $block->counselor_id ? ($block->counselor->name ?? 'Unknown') : 'Global Block (All Users)' }}
                                @if($block->reason) <span class="text-xs text-gray-500 ml-2">({{ $block->reason }})</span> @endif
                            </p>
                            <p class="text-sm text-gray-500 truncate">{{ \Carbon\Carbon::parse($block->date)->format('M d, Y') }} from {{ \Carbon\Carbon::parse($block->start_time)->format('g:i A') }} to {{ \Carbon\Carbon::parse($block->end_time)->format('g:i A') }}</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.schedule.block.destroy', $block->id) }}" method="POST" class="self-end sm:self-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
                @empty
                <div class="text-center py-4 text-gray-500 text-sm">
                    No block times schedule.
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
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 border border-gray-100 rounded-xl hover:border-[#f48545]/30 hover:bg-[#fff9f6] transition-colors shadow-sm gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-[#fef4ee] rounded-full flex items-center justify-center shrink-0">
                            <i data-lucide="graduation-cap" class="w-5 h-5 text-[#f48545]"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $sessReq->student->name ?? 'Unknown Student' }}</p>
                            <p class="text-sm text-gray-500">{{ $sessReq->type }} - Requested {{ $sessReq->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto ml-14 sm:ml-0">
                        @if($sessReq->is_walk_in)
                            <span class="text-xs px-2.5 py-1 font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-100">Walk-in</span>
                        @endif
                        <span class="text-xs px-2.5 py-1 font-medium rounded-full {{ $sessReq->urgency == 'High' ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-[#fff1eb] text-[#d9733a] border border-[#fef4ee]' }}">{{ $sessReq->urgency }} Priority</span>
                        
                        @if($sessReq->is_walk_in)
                            <form action="/admin/schedule/walkin/{{ $sessReq->id }}/approve" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-emerald-500 text-white hover:bg-emerald-600 h-8 px-4 transition-colors shadow-sm">Approve Walk-in</button>
                            </form>
                        @else
                            <button onclick="openAssignModal({{ $sessReq->id }})" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-[#f48545] text-white hover:bg-[#e67a3b] h-8 px-4 transition-colors shadow-sm">Assign Counselor</button>
                        @endif
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
