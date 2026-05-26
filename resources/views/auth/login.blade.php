@extends('layouts.app')

@section('content')
<style>
    @import url('https://api.fontshare.com/v2/css?f[]=switzer@400,500,600,700,800,900&display=swap');

    body {
        font-family: 'Switzer', sans-serif !important;
    }
</style>

<!-- Full screen scrollable wrapper to override layout overflow -->
<div class="h-screen w-screen overflow-y-auto bg-gradient-to-br from-[#2b1810] via-[#5e3020] to-[#7f4630] flex items-center justify-center p-4 sm:p-8 md:p-12">
    <!-- Main Card -->
    <div class="bg-white/95 backdrop-blur-md w-full max-w-[1000px] rounded-[2.5rem] flex flex-col lg:flex-row shadow-2xl overflow-hidden border border-white/20 my-auto">
        
        <!-- Left Side: Login Form -->
        <div class="w-full lg:w-7/12 p-8 sm:p-12 md:p-14 flex flex-col justify-center order-2 lg:order-1">
            
            <!-- Mobile Logo (shown only on small screens) -->
            <div class="flex lg:hidden items-center gap-2 mb-6">
                <div class="w-8 h-8 flex items-center justify-center bg-[#5e3020]/10 rounded-lg p-1.5">
                    <img src="{{ asset('assets/icons/stusion_logo.png') }}" alt="Stusion Logo" class="w-full h-full object-contain">
                </div>
                <h1 class="text-lg font-black tracking-wider"><span class="text-[#2d2d2d]">STU</span><span class="text-[#f48545]">SION</span></h1>
            </div>

            <!-- Form Headers -->
            <div class="mb-8">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Welcome Back</h2>
                <p class="text-sm text-gray-500 mt-1.5">Sign in to your account to continue managing your counseling sessions.</p>
            </div>

            <!-- Error Notification -->
            @if ($errors->any())
            <div class="p-4 mb-5 text-xs font-semibold text-red-800 rounded-xl bg-red-50 border border-red-200" role="alert">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-red-500 shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            </div>
            @endif

            <!-- Form -->
            <form method="POST" action="/login" class="space-y-5 w-full">
                @csrf
                
                <!-- Username Field -->
                <div class="flex flex-col">
                    <label for="username" class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="at-sign" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            id="username"
                            name="username" 
                            value="{{ old('username') }}"
                            placeholder="yourusername" 
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 border border-gray-200 text-gray-800 placeholder-gray-400 text-sm rounded-xl focus:border-[#f48545] focus:ring-2 focus:ring-[#f48545]/20 focus:outline-none transition-all shadow-sm"
                            required 
                        />
                    </div>
                </div>

                <!-- Password Field -->
                <div class="flex flex-col">
                    <label for="password" class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <input 
                            type="password" 
                            id="password"
                            name="password" 
                            placeholder="••••••••" 
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 border border-gray-200 text-gray-800 placeholder-gray-400 text-sm rounded-xl focus:border-[#f48545] focus:ring-2 focus:ring-[#f48545]/20 focus:outline-none transition-all shadow-sm"
                            required 
                        />
                    </div>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit" 
                    class="w-full mt-4 bg-gradient-to-r from-[#f48545] to-[#e67a3b] hover:from-[#e67a3b] hover:to-[#d5692a] text-white py-3 rounded-xl text-xs font-bold tracking-wider uppercase transition-all duration-300 shadow-md hover:shadow-lg active:scale-[0.98]"
                >
                    Log In
                </button>

                <!-- Redirect to Register Link -->
                <p class="text-center text-xs text-gray-500 font-semibold mt-4">
                    Don't have an account? <a href="/register" class="text-[#f48545] hover:text-[#e67a3b] hover:underline font-bold transition-all ml-1">Register</a>
                </p>
            </form>
        </div>

        <!-- Right Side: Brand Panel (Hidden on small screens, shown on large) -->
        <div class="hidden lg:flex lg:w-5/12 bg-gradient-to-b from-[#4d2518] to-[#25120c] text-white p-12 flex-col justify-between relative overflow-hidden order-1 lg:order-2">
            <!-- Decorative light glows -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-[#f48545]/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-[#f48545]/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <!-- Logo Section -->
            <div class="flex items-center gap-3 z-10 justify-end">
                <h1 class="text-2xl font-black tracking-wider"><span class="text-[#f5f5f5]">STU</span><span class="text-[#f48545]">SION</span></h1>
                <div class="w-10 h-10 flex items-center justify-center bg-white/10 rounded-xl p-2 border border-white/20 shadow-inner">
                    <img src="{{ asset('assets/icons/stusion_logo.png') }}" alt="Stusion Logo" class="w-full h-full object-contain">
                </div>
            </div>

            <!-- Central Quote/Graphics Panel -->
            <div class="my-auto py-10 z-10 text-right">
                <i data-lucide="quote" class="w-10 h-10 text-[#f48545]/40 ml-auto mb-4 rotate-180"></i>
                <h2 class="text-2xl font-bold leading-relaxed mb-6 italic text-[#f5f5f5]">
                    "Academic counseling is the bridge between student potential and lifetime achievement."
                </h2>
                <div class="flex items-center gap-3 justify-end">
                    <span class="w-8 h-px bg-white/30"></span>
                    <span class="text-xs tracking-widest uppercase text-[#f48545] font-bold">Stusion Guidance Team</span>
                </div>
            </div>

            <!-- Footer Text -->
            <p class="text-xs text-gray-400 z-10 text-right">
                &copy; {{ date('Y') }} Stusion. All rights reserved.
            </p>
        </div>
        
    </div>
</div>
@endsection
