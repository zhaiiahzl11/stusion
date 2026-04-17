@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 w-full">
    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Header -->
        @include('layouts.header')

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto">
            @yield('dashboard_content')
        </main>
    </div>
</div>
@endsection
