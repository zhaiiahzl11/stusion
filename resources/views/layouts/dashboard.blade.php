@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 w-full relative overflow-hidden">
    <!-- Mobile Sidebar Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-gray-900/50 z-40 hidden md:hidden transition-opacity" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <div id="sidebar-container" class="fixed md:static inset-y-0 left-0 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out w-64 h-full">
        @include('layouts.sidebar')
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden h-screen w-full">
        <!-- Header -->
        @include('layouts.header')

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto w-full">
            @yield('dashboard_content')
        </main>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-container');
        const overlay = document.getElementById('mobile-overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>
@endsection
