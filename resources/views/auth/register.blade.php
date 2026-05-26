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
    <div class="bg-white/95 backdrop-blur-md w-full max-w-[1050px] rounded-[2.5rem] flex flex-col lg:flex-row shadow-2xl overflow-hidden border border-white/20 my-auto">
        
        <!-- Left Side: Brand Panel (Hidden on small screens, shown on large) -->
        <div class="hidden lg:flex lg:w-5/12 bg-gradient-to-b from-[#4d2518] to-[#25120c] text-white p-12 flex-col justify-between relative overflow-hidden">
            <!-- Decorative light glows -->
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-[#f48545]/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-[#f48545]/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <!-- Logo Section -->
            <div class="flex items-center gap-3 z-10">
                <div class="w-10 h-10 flex items-center justify-center bg-white/10 rounded-xl p-2 border border-white/20 shadow-inner">
                    <img src="{{ asset('assets/icons/stusion_logo.png') }}" alt="Stusion Logo" class="w-full h-full object-contain">
                </div>
                <h1 class="text-2xl font-black tracking-wider"><span class="text-[#f5f5f5]">STU</span><span class="text-[#f48545]">SION</span></h1>
            </div>

            <!-- Features & Tagline Section -->
            <div class="my-auto py-10 z-10">
                <h2 class="text-3xl font-extrabold leading-tight mb-4 text-[#f5f5f5]">
                    Your Academic Guidance & Counseling Hub
                </h2>
                <p class="text-sm text-gray-300 mb-8 leading-relaxed">
                    Unlocking student potential and providing secure, streamlined communication between students and professional counselors.
                </p>

                <!-- Feature list -->
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center shrink-0 border border-white/10 shadow-sm">
                            <i data-lucide="calendar" class="w-5 h-5 text-[#f48545]"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-[#f5f5f5]">Seamless Scheduling</h3>
                            <p class="text-xs text-gray-300 mt-0.5">Book virtual or physical sessions synced directly with counselor schedules.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center shrink-0 border border-white/10 shadow-sm">
                            <i data-lucide="shield-check" class="w-5 h-5 text-[#f48545]"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-[#f5f5f5]">Secure & Confidential</h3>
                            <p class="text-xs text-gray-300 mt-0.5">Your sessions, notes, and records are fully protected and private.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center shrink-0 border border-white/10 shadow-sm">
                            <i data-lucide="activity" class="w-5 h-5 text-[#f48545]"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-[#f5f5f5]">Live Status Tracking</h3>
                            <p class="text-xs text-gray-300 mt-0.5">Get immediate updates on availability approvals and walk-in check-ins.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Text -->
            <p class="text-xs text-gray-400 z-10">
                &copy; {{ date('Y') }} Stusion. All rights reserved.
            </p>
        </div>
        
        <!-- Right Side: Registration Form -->
        <div class="w-full lg:w-7/12 p-8 sm:p-12 md:p-14 flex flex-col justify-center">
            
            <!-- Mobile Logo (shown only on small screens) -->
            <div class="flex lg:hidden items-center gap-2 mb-6">
                <div class="w-8 h-8 flex items-center justify-center bg-[#5e3020]/10 rounded-lg p-1.5">
                    <img src="{{ asset('assets/icons/stusion_logo.png') }}" alt="Stusion Logo" class="w-full h-full object-contain">
                </div>
                <h1 class="text-lg font-black tracking-wider"><span class="text-[#2d2d2d]">STU</span><span class="text-[#f48545]">SION</span></h1>
            </div>

            <!-- Form Headers -->
            <div class="mb-8">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Create an Account</h2>
                <p class="text-sm text-gray-500 mt-1.5">Join the Stusion platform today. It takes less than a minute.</p>
            </div>

            <!-- Form -->
            <form method="POST" action="/register" class="space-y-5 w-full">
                @csrf
                
                <!-- Responsive Grid: Full Name & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Full Name Field -->
                    <div class="flex flex-col">
                        <label for="name" class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Full Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i data-lucide="user" class="w-4 h-4 text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="name"
                                name="name" 
                                value="{{ old('name') }}"
                                placeholder="John Doe" 
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 border border-gray-200 text-gray-800 placeholder-gray-400 text-sm rounded-xl focus:border-[#f48545] focus:ring-2 focus:ring-[#f48545]/20 focus:outline-none transition-all shadow-sm"
                                required 
                            />
                        </div>
                        @error('name')
                            <span class="text-red-500 text-[11px] mt-1 font-semibold flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Email Address Field -->
                    <div class="flex flex-col">
                        <label for="email" class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i data-lucide="mail" class="w-4 h-4 text-gray-400"></i>
                            </div>
                            <input 
                                type="email" 
                                id="email"
                                name="email" 
                                value="{{ old('email') }}"
                                placeholder="john@example.com" 
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 border border-gray-200 text-gray-800 placeholder-gray-400 text-sm rounded-xl focus:border-[#f48545] focus:ring-2 focus:ring-[#f48545]/20 focus:outline-none transition-all shadow-sm"
                                required 
                            />
                        </div>
                        @error('email')
                            <span class="text-red-500 text-[11px] mt-1 font-semibold flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <!-- Responsive Grid: Username & User Role -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                                placeholder="johndoe" 
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 border border-gray-200 text-gray-800 placeholder-gray-400 text-sm rounded-xl focus:border-[#f48545] focus:ring-2 focus:ring-[#f48545]/20 focus:outline-none transition-all shadow-sm"
                                required 
                            />
                        </div>
                        @error('username')
                            <span class="text-red-500 text-[11px] mt-1 font-semibold flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- User Role / Register As Field -->
                    <div class="flex flex-col">
                        <label for="role" class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Register As</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i data-lucide="users" class="w-4 h-4 text-gray-400"></i>
                            </div>
                            <select 
                                id="role"
                                name="role" 
                                class="w-full pl-10 pr-10 py-2.5 bg-gray-50/50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:border-[#f48545] focus:ring-2 focus:ring-[#f48545]/20 focus:outline-none transition-all shadow-sm appearance-none" 
                                required
                            >
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select your role...</option>
                                <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                                <option value="counselor" {{ old('role') === 'counselor' ? 'selected' : '' }}>Counselor</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <!-- Custom dropdown indicator -->
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                        @error('role')
                            <span class="text-red-500 text-[11px] mt-1 font-semibold flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <!-- Responsive Grid: Password & Confirm Password -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                        @error('password')
                            <span class="text-red-500 text-[11px] mt-1 font-semibold flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="flex flex-col">
                        <label for="password_confirmation" class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i data-lucide="shield-check" class="w-4 h-4 text-gray-400"></i>
                            </div>
                            <input 
                                type="password" 
                                id="password_confirmation"
                                name="password_confirmation" 
                                placeholder="••••••••" 
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 border border-gray-200 text-gray-800 placeholder-gray-400 text-sm rounded-xl focus:border-[#f48545] focus:ring-2 focus:ring-[#f48545]/20 focus:outline-none transition-all shadow-sm"
                                required 
                            />
                        </div>
                    </div>
                </div>

                <!-- Create Account Button -->
                <button 
                    type="submit" 
                    class="w-full mt-4 bg-gradient-to-r from-[#f48545] to-[#e67a3b] hover:from-[#e67a3b] hover:to-[#d5692a] text-white py-3 rounded-xl text-xs font-bold tracking-wider uppercase transition-all duration-300 shadow-md hover:shadow-lg active:scale-[0.98]"
                >
                    Create Account
                </button>

                <!-- Redirect to Login Link -->
                <p class="text-center text-xs text-gray-500 font-semibold mt-4">
                    Already have an account? <a href="{{ route('login') }}" class="text-[#f48545] hover:text-[#e67a3b] hover:underline font-bold transition-all ml-1">Log in</a>
                </p>
            </form>
        </div>
        
    </div>
</div>
@endsection
