@extends('layouts.dashboard', ['role' => 'counselor', 'activeModule' => 'walkin', 'title' => 'Walk-in Intake', 'subtitle' => 'Manually create a session for a walk-in student'])

@section('dashboard_content')
<div class="p-6 space-y-6">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-day.blocked-date {
            background-color: #fee2e2 !important;
            color: #ef4444 !important;
            font-weight: bold;
            border-color: #fca5a5 !important;
        }
    </style>

    <div class="flex items-start gap-3 p-4 bg-[#fff1eb] border border-[#fef4ee] shadow-sm rounded-xl">
        <i data-lucide="info" class="w-5 h-5 text-[#f48545] mt-0.5"></i>
        <div>
            <p class="font-bold text-[#d9733a]">Walk-in Process</p>
            <p class="text-sm text-[#f48545] mt-1">
                Enter the student's details below. If the email is new, a temporary account will be automatically created.
                The selected time slot will be instantly blocked, but the session must be approved by an Admin.
            </p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold leading-none tracking-tight">Student Details & Scheduling</h3>
        </div>
        <form method="POST" action="/counselor/walk-in" class="p-6 space-y-4" onsubmit="document.getElementById('submitBtn').disabled = true; document.getElementById('submitBtn').innerText = 'Submitting...';">
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-gray-900">Student Name</label>
                    <input type="text" name="student_name" class="mt-2 w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#f48545]/50 transition-shadow" placeholder="e.g. John Doe" required>
                </div>
                <div>
                    <label class="text-sm font-bold text-gray-900">Student Email</label>
                    <input type="email" name="student_email" class="mt-2 w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#f48545]/50 transition-shadow" placeholder="student@example.com" required>
                </div>
            </div>
            
            <div class="mt-5">
                <label class="text-sm font-bold text-gray-900">Type of Counseling</label>
                <select name="type" class="mt-2 w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#f48545]/50 transition-shadow" required>
                    <option value="" disabled selected>Select a type</option>
                    <option value="Academic Stress">Academic Stress</option>
                    <option value="Career Guidance">Career Guidance</option>
                    <option value="Personal Issues">Personal Issues</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                <div>
                    <label class="text-sm font-bold text-gray-900">Date</label>
                    <input type="text" name="preferred_date" id="date_input" placeholder="Select Date" class="mt-2 w-full h-10 bg-white border border-gray-200 rounded-lg px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#f48545]/50 transition-shadow cursor-pointer" required>
                </div>
                <div>
                    <label class="text-sm font-bold text-gray-900">Time</label>
                    <input type="hidden" name="preferred_time" id="time_input" required>
                    <div id="time_grid" class="mt-2 grid grid-cols-3 sm:grid-cols-4 gap-2">
                        <div class="col-span-full text-sm text-gray-500 italic py-2">Select Date First</div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <label class="text-sm font-bold text-gray-900">Notes / Description</label>
                <textarea name="description" class="mt-2 w-full h-24 bg-gray-50 border border-gray-200 rounded-lg p-3 text-sm text-gray-900 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-[#f48545]/50 transition-shadow" placeholder="Initial observation or reason for walk-in..." required></textarea>
            </div>
            
            <div class="pt-6 flex justify-end">
                <button type="submit" id="submitBtn" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-[#f48545] text-white hover:bg-[#e67a3b] hover:shadow-md transition-all h-10 px-6 py-2 disabled:opacity-70 disabled:cursor-not-allowed">
                    Submit for Approval
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    const counselor = @json($counselor);
    const globalBlockedTimes = @json($globalBlockedTimes);

    function isGlobalBlocked(dateStr) {
        return globalBlockedTimes.some(block => block.date === dateStr);
    }

    function formatDate(dateObj) {
        const year = dateObj.getFullYear();
        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
        const day = String(dateObj.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    const datePicker = flatpickr("#date_input", {
        minDate: "today",
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            const date = dayElem.dateObj;
            if (!date) return;
            const dateStr = formatDate(date);
            
            if (date.getDay() === 0 || date.getDay() === 6 || isGlobalBlocked(dateStr)) {
                dayElem.classList.add("blocked-date");
                return;
            }

            const hasFullBlock = counselor.blocked_times.some(block => {
                return block.date === dateStr && block.start_time <= '09:00:00' && block.end_time >= '17:00:00';
            });
            if (hasFullBlock) {
                dayElem.classList.add("blocked-date");
            }
        },
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 0) return;
            const dateObj = selectedDates[0];
            const dateVal = formatDate(dateObj);
            const day = dateObj.getDay();

            if (day === 0 || day === 6) {
                alert('Saturdays and Sundays are not available.');
                instance.clear();
                return;
            }

            if (isGlobalBlocked(dateVal)) {
                alert('This date is globally blocked.');
                instance.clear();
                return;
            }

            const hasFullBlock = counselor.blocked_times.some(block => {
                return block.date === dateVal && block.start_time <= '09:00:00' && block.end_time >= '17:00:00';
            });
            if (hasFullBlock) {
                alert('You are fully unavailable on this date.');
                instance.clear();
                return;
            }
            
            renderTimeOptions();
        }
    });

    const timeInput = document.getElementById('time_input');
    const timeGrid = document.getElementById('time_grid');

    function formatTime12Hr(h, m) {
        const ampm = h >= 12 ? 'PM' : 'AM';
        const hour12 = h % 12 || 12;
        return `${hour12}:${m} ${ampm}`;
    }

    function renderTimeOptions() {
        const dateVal = document.getElementById('date_input').value;

        timeGrid.innerHTML = '';
        timeInput.value = '';

        if (!dateVal) {
            timeGrid.innerHTML = '<div class="col-span-full text-sm text-gray-500 italic py-2">Select Date First</div>';
            return;
        }

        const startHour = 8;
        const endHour = 17;

        for (let h = startHour; h <= endHour; h++) {
            ['00'].forEach(m => {
                const hourStr = String(h).padStart(2, '0');
                const timeVal = `${hourStr}:${m}`;
                const displayTime = formatTime12Hr(h, m);
                
                let isBlocked = false;

                const isBooked = counselor.counseling_sessions.some(session => 
                    session.date === dateVal && session.time.substring(0, 5) === timeVal
                );

                if (isBooked) {
                    isBlocked = true;
                }

                if (!isBlocked) {
                    const hasBlock = counselor.blocked_times.some(block => {
                        const sTime = block.start_time.substring(0, 5);
                        const eTime = block.end_time.substring(0, 5);
                        return block.date === dateVal && sTime <= timeVal && eTime >= timeVal;
                    });
                    if (hasBlock) {
                        isBlocked = true;
                    }
                }

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = displayTime;
                
                if (isBlocked) {
                    btn.disabled = true;
                    btn.className = 'w-full h-10 rounded-lg text-sm font-bold bg-red-50 text-red-500 border border-red-200 cursor-not-allowed opacity-80';
                } else {
                    btn.className = 'time-slot-btn w-full h-10 rounded-lg text-sm font-medium bg-white text-gray-700 border border-gray-200 hover:border-[#f48545] hover:text-[#f48545] transition-colors';
                    btn.onclick = () => {
                        document.querySelectorAll('.time-slot-btn').forEach(b => {
                            b.classList.remove('bg-[#f48545]', 'text-white', 'border-[#f48545]');
                            b.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
                        });
                        btn.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');
                        btn.classList.add('bg-[#f48545]', 'text-white', 'border-[#f48545]');
                        timeInput.value = timeVal;
                    };
                }
                
                timeGrid.appendChild(btn);
            });
        }
    }
</script>
@endsection
