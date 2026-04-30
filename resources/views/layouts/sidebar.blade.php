@php
    $adminModules = [
        ['url' => '/admin/dashboard', 'id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
        ['url' => '/admin/users', 'id' => 'users', 'label' => 'Manage Users', 'icon' => 'users'],
        ['url' => '/admin/schedule', 'id' => 'schedule', 'label' => 'Schedule Control', 'icon' => 'calendar'],
    ];

    $counselorModules = [
        ['url' => '/counselor/dashboard', 'id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
        ['url' => '/counselor/sessions', 'id' => 'sessions', 'label' => 'My Sessions', 'icon' => 'clipboard-list'],
        ['url' => '/counselor/availability', 'id' => 'availability', 'label' => 'Leave Request', 'icon' => 'clock'],
        ['url' => '/counselor/walk-in', 'id' => 'walkin', 'label' => 'Walk-in Intake', 'icon' => 'user-plus'],
    ];

    $studentModules = [
        ['url' => '/student/dashboard', 'id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
        ['url' => '/student/request', 'id' => 'request', 'label' => 'Request Session', 'icon' => 'send'],
        ['url' => '/student/sessions', 'id' => 'sessions', 'label' => 'My Sessions', 'icon' => 'file-text'],
    ];

    $modules = $role === 'admin' ? $adminModules : ($role === 'counselor' ? $counselorModules : $studentModules);
    $roleLabel = ucfirst($role);
    $roleIcon = $role === 'admin' ? 'shield' : ($role === 'counselor' ? 'user' : 'graduation-cap');
    
    $user = Auth::guard($role)->user();
    $name = $user ? $user->name : 'Unknown User';
    $email = $user ? $user->email : 'unknown@stusion.com';
@endphp

<div class="w-64 bg-[#5e3020] text-white flex flex-col h-full shrink-0">
    <!-- Logo -->
    <div class="p-6 border-b border-gray-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex items-center justify-center">
                <img src="{{ asset('assets/icons/stusion_logo.png') }}" alt="Stusion Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <h1 class="font-bold text-lg tracking-wider"><span class="text-[#f5f5f5]">STU</span><span class="text-[#f48545]">SION</span></h1>
                <p class="text-xs text-gray-300">Session System</p>
            </div>
        </div>
    </div>

    <!-- Role Badge -->
    <div class="px-4 py-3 border-b border-[#f48545]/20">
        <div class="flex items-center gap-2 px-3 py-2 bg-[#f48545]/10 rounded-lg text-[#f48545]">
            <i data-lucide="{{ $roleIcon }}" class="w-4 h-4"></i>
            <span class="text-sm font-medium text-white">{{ $roleLabel }} Panel</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1">
        @foreach($modules as $module)
            <a href="{{ $module['url'] }}"
               class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
               {{ (isset($activeModule) && $activeModule === $module['id'])
                   ? 'bg-[#f48545] text-white'
                   : 'text-gray-300 hover:bg-[#f48545]/20 hover:text-[#f48545]' }}">
                <i data-lucide="{{ $module['icon'] }}" class="w-5 h-5"></i>
                {{ $module['label'] }}
            </a>
        @endforeach
    </nav>

    <!-- User Section -->
    <div class="p-4 border-t border-[#f48545]/20">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-[#f48545]/20 rounded-full flex items-center justify-center">
                <i data-lucide="user" class="w-5 h-5 text-[#f48545]"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate text-white">
                    {{ $name }}
                </p>
                <p class="text-xs text-gray-400 truncate">
                    {{ $email }}
                </p>
            </div>
        </div>
        <a href="/" class="w-full flex items-center justify-center gap-2 py-2 rounded-md border border-[#f48545]/30 text-sm text-[#f48545] hover:bg-[#f48545] hover:text-white hover:border-[#f48545] transition-colors">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            Logout
        </a>
    </div>
</div>
