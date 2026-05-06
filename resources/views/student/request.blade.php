@extends('layouts.dashboard', ['role' => 'student', 'activeModule' => 'request', 'title' => 'Request Session', 'subtitle' => 'Submit a new counseling request'])

@section('dashboard_content')
<div class="p-6 space-y-6">
    <!-- Include Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-day.blocked-date {
            background-color: #fee2e2 !important; /* bg-red-100 */
            color: #ef4444 !important; /* text-red-500 */
            font-weight: bold;
            border-color: #fca5a5 !important; /* border-red-300 */
        }
    </style>

    <!-- Info Banner -->
    <div class="flex items-start gap-3 p-4 bg-[#fff1eb] border border-[#fef4ee] shadow-sm rounded-xl">
        <i data-lucide="alert-circle" class="w-5 h-5 text-[#f48545] mt-0.5"></i>
        <div>
            <p class="font-bold text-[#d9733a]">How it works</p>
            <p class="text-sm text-[#f48545] mt-1">
                Select a counselor, date, and time. Your session will be automatically scheduled, provided the counselor is available. Please note that bookings must be made at least one day in advance.
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
                <label class="text-sm font-bold text-gray-900">Select Counselor</label>
                <select name="counselor_id" id="counselor_select" class="mt-2 w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#f48545]/50 transition-shadow" required>
                    <option value="" disabled selected>Select a counselor</option>
                    @foreach($counselors as $counselor)
                        <option value="{{ $counselor->id }}">{{ $counselor->name }}</option>
                    @endforeach
                </select>
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
                    <label class="text-sm font-bold text-gray-900">Preferred Date</label>
                    <input type="text" name="preferred_date" id="date_input" placeholder="Select Date" class="mt-2 w-full h-10 bg-white border border-gray-200 rounded-lg px-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#f48545]/50 transition-shadow cursor-pointer" required>
                </div>
                <div>
                    <label class="text-sm font-bold text-gray-900">Preferred Time</label>
                    <input type="hidden" name="preferred_time" id="time_input" required>
                    <div id="time_grid" class="mt-2 grid grid-cols-3 sm:grid-cols-4 gap-2">
                        <div class="col-span-full text-sm text-gray-500 italic py-2">Select Date & Counselor First</div>
                    </div>
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

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    const counselors = @json($counselors);
    const globalBlockedTimes = @json($globalBlockedTimes);

    const counselorSelect = document.getElementById('counselor_select');

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
        minDate: new Date().fp_incr(1),
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            const date = dayElem.dateObj;
            if (!date) return;
            const dateStr = formatDate(date);
            
            // Mark weekends and global blocked dates in red
            if (date.getDay() === 0 || date.getDay() === 6 || isGlobalBlocked(dateStr)) {
                dayElem.classList.add("blocked-date");
                return;
            }

            // Mark counselor specific blocks (full day)
            const counselorId = parseInt(counselorSelect.value);
            if (counselorId) {
                const counselor = counselors.find(c => c.id === counselorId);
                if (counselor) {
                    const hasFullBlock = counselor.blocked_times.some(block => {
                        return block.date === dateStr && block.start_time <= '09:00:00' && block.end_time >= '17:00:00';
                    });
                    if (hasFullBlock) {
                        dayElem.classList.add("blocked-date");
                    }
                }
            }
        },
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 0) return;
            const dateObj = selectedDates[0];
            const dateVal = formatDate(dateObj);
            const day = dateObj.getDay();

            if (day === 0 || day === 6) {
                alert('Saturdays and Sundays are not available. Please select another date.');
                instance.clear();
                return;
            }

            if (isGlobalBlocked(dateVal)) {
                alert('This date is currently blocked and unavailable. Please select another date.');
                instance.clear();
                return;
            }

            const counselorId = parseInt(counselorSelect.value);
            if (counselorId) {
                const counselor = counselors.find(c => c.id === counselorId);
                if (counselor) {
                    const hasFullBlock = counselor.blocked_times.some(block => {
                        return block.date === dateVal && block.start_time <= '09:00:00' && block.end_time >= '17:00:00';
                    });
                    if (hasFullBlock) {
                        alert('The selected counselor is unavailable for this entire date.');
                        instance.clear();
                        return;
                    }
                }
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
        const counselorId = parseInt(counselorSelect.value);

        timeGrid.innerHTML = '';
        timeInput.value = ''; // Reset selection

        if (!dateVal || !counselorId) {
            timeGrid.innerHTML = '<div class="col-span-full text-sm text-gray-500 italic py-2">Select Date & Counselor First</div>';
            return;
        }

        const counselor = counselors.find(c => c.id === counselorId);
        if (!counselor) return;

        const startHour = 8;
        const endHour = 17;

        for (let h = startHour; h <= endHour; h++) {
            // Hourly format only
            ['00'].forEach(m => {
                const hourStr = String(h).padStart(2, '0');
                const timeVal = `${hourStr}:${m}`;
                const displayTime = formatTime12Hr(h, m);
                
                let isBlocked = false;

                // Check Booked Sessions
                const isBooked = counselor.counseling_sessions.some(session => 
                    session.date === dateVal && session.time.substring(0, 5) === timeVal
                );

                if (isBooked) {
                    isBlocked = true;
                }

                // Check Blocked Times (Unavailability Requests)
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
                        // Deselect all
                        document.querySelectorAll('.time-slot-btn').forEach(b => {
                            b.classList.remove('bg-[#f48545]', 'text-white', 'border-[#f48545]');
                            b.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
                        });
                        // Select this
                        btn.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');
                        btn.classList.add('bg-[#f48545]', 'text-white', 'border-[#f48545]');
                        timeInput.value = timeVal;
                    };
                }
                
                timeGrid.appendChild(btn);
            });
        }
    }

    counselorSelect.addEventListener('change', () => {
        // Redraw dates to show counselor specific full-day blocks
        datePicker.redraw();
        renderTimeOptions();
    });

</script>
@endsection
