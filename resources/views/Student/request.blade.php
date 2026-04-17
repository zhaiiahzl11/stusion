@extends('layouts.dashboard', ['role' => 'student', 'activeModule' => 'request', 'title' => 'Request Session', 'subtitle' => 'Submit a new counseling request'])

@section('dashboard_content')
<div class="p-6 space-y-6">
    <!-- Info Banner -->
    <div class="flex items-start gap-3 p-4 bg-[#fff1eb] border border-[#fef4ee] shadow-sm rounded-xl">
        <i data-lucide="alert-circle" class="w-5 h-5 text-[#f48545] mt-0.5"></i>
        <div>
            <p class="font-bold text-[#d9733a]">How it works</p>
            <p class="text-sm text-[#f48545] mt-1">
                Submit your counseling request below. An admin will review it and assign a suitable counselor based on availability.
                You will be notified once your session is scheduled.
            </p>
        </div>
    </div>

    <!-- Request Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold leading-none tracking-tight">Submit Counseling Request</h3>
        </div>
        <form method="POST" action="/student/request" class="p-6 space-y-4">
            @csrf
            
            @if(session('success'))
            <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50" role="alert">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div>
                <label class="text-sm font-bold text-gray-900">Type of Counseling</label>
                <select name="type" class="mt-2 w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#f48545]/50 transition-shadow" required>
                    <option value="" disabled selected>Select a type</option>
                    <option value="Academic Stress">Academic Stress</option>
                    <option value="Career Guidance">Career Guidance</option>
                    <option value="Personal Issues">Personal Issues</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mt-5">
                <div>
                    <label class="text-sm font-bold text-gray-900">Preferred Date (Optional)</label>
                    <input type="date" name="preferred_date" class="mt-2 w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#f48545]/50 transition-shadow">
                </div>
                <div>
                    <label class="text-sm font-bold text-gray-900">Preferred Time (Optional)</label>
                    <input type="time" name="preferred_time" class="mt-2 w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#f48545]/50 transition-shadow">
                </div>
            </div>
            
            <div class="mt-5">
                <label class="text-sm font-bold text-gray-900">Urgency</label>
                <select name="urgency" class="mt-2 w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#f48545]/50 transition-shadow" required>
                    <option value="Low">Low</option>
                    <option value="Medium" selected>Medium</option>
                    <option value="High">High</option>
                </select>
            </div>

            <div class="mt-5">
                <label class="text-sm font-bold text-gray-900">Description</label>
                <textarea name="description" class="mt-2 w-full h-24 bg-gray-50 border border-gray-200 rounded-lg p-3 text-sm text-gray-900 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-[#f48545]/50 transition-shadow" placeholder="Briefly describe what you'd like to discuss..." required></textarea>
            </div>
            
            <div class="pt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-[#f48545] text-white hover:bg-[#e67a3b] hover:shadow-md transition-all h-10 px-6 py-2">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
