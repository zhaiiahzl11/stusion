<header class="bg-white border-b border-gray-200 px-4 md:px-6 py-4 flex items-center justify-between sticky top-0 z-30">
    <div class="flex items-center gap-3">
        <button onclick="toggleSidebar()" class="md:hidden p-2 -ml-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <div>
            <h1 class="text-lg md:text-xl font-bold text-gray-900 truncate">{{ $title ?? 'Dashboard' }}</h1>
            @if(isset($subtitle))
                <p class="hidden sm:block text-sm text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-3 relative" id="notification-container">
        <button onclick="toggleNotifications()" class="relative p-2 rounded-lg hover:bg-gray-100 transition-colors focus:bg-gray-100 focus:outline-none">
            <i data-lucide="bell" class="w-5 h-5 text-gray-500"></i>
            <span class="absolute top-1 right-1 w-2 h-2 bg-[#f48545] rounded-full"></span>
        </button>

        <!-- Dropdown Panel -->
        <div id="notification-dropdown" class="hidden absolute top-full right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="font-semibold text-gray-900 text-sm">Notifications</h3>
                <span class="text-xs font-medium bg-[#fff1eb] text-[#d9733a] px-2 py-0.5 rounded-full">New</span>
            </div>
            
            <div class="max-h-80 overflow-y-auto">
                @php
                    // Fetch generic notifications based on role if possible
                    $alerts = collect();
                    if(isset($role) && $role === 'admin') {
                        $pending = \App\Models\SessionRequest::where('status', 'pending')->with('student')->latest()->take(3)->get();
                        foreach($pending as $p) {
                            $alerts->push(['title' => 'New Session Request', 'desc' => 'From ' . ($p->student->name ?? 'Unknown'), 'time' => $p->created_at->diffForHumans(), 'link' => '/admin/schedule']);
                        }
                    }
                @endphp

                @forelse($alerts as $alert)
                <a href="{{ $alert['link'] }}" class="block p-4 border-b border-gray-50 hover:bg-[#fff9f6] transition-colors">
                    <p class="text-sm font-medium text-gray-900">{{ $alert['title'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $alert['desc'] }}</p>
                    <p class="text-[10px] text-[#f48545] mt-1">{{ $alert['time'] }}</p>
                </a>
                @empty
                <div class="p-4 text-center">
                    <p class="text-sm text-gray-500">You're all caught up!</p>
                </div>
                @endforelse
            </div>
            
            @if(isset($role) && $role === 'admin')
            <a href="/admin/schedule" class="block bg-gray-50 px-4 py-2.5 text-center text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                View all activity
            </a>
            @endif
        </div>
    </div>
</header>

<script>
    function toggleNotifications() {
        const dropdown = document.getElementById('notification-dropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close when clicking outside
    document.addEventListener('click', function(event) {
        const container = document.getElementById('notification-container');
        const dropdown = document.getElementById('notification-dropdown');
        
        if (!container.contains(event.target) && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
        }
    });
</script>
