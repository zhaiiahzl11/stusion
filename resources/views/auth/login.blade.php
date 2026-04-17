@extends('layouts.app')

@section('content')
<style>
    @import url('https://api.fontshare.com/v2/css?f[]=switzer@400,500,600,700,800,900&display=swap');

    body {
        font-family: 'Switzer', sans-serif !important;
    }
</style>

<div class="min-h-screen w-full flex items-center justify-center p-6 sm:p-12 relative bg-[#5e3020]">
  <!-- Main Card -->
  <div class="bg-[#ebebeb] w-full max-w-[900px] rounded-[2rem] flex flex-col md:flex-row shadow-lg min-h-[440px] md:min-h-[500px]">
    
    <!-- Left Side (Form) -->
    <div class="flex flex-col justify-center px-10 py-12 md:px-16 md:py-16 w-full md:w-1/2">
      
      <!-- Text Logo -->
      <div class="mb-8 flex items-center pl-2">
          <h1 class="text-[32px] md:text-[40px] font-black tracking-tighter"><span class="text-[#2d2d2d]">STU</span><span class="text-[#f48545]">SION</span></h1>
      </div>

      <!-- Form -->
      <form method="POST" action="/login" class="space-y-4 w-full pl-2">
        @csrf
        
        <!-- Username Input -->
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <i data-lucide="user" class="w-4 h-4 text-gray-500"></i>
          </div>
          <input 
            type="text" 
            name="username" 
            placeholder="Username" 
            class="w-full pl-12 pr-4 py-3 bg-[#f5f5f5] text-gray-800 placeholder-gray-400 text-xs font-semibold rounded-xl border-none focus:ring-2 focus:ring-[#ff883e] focus:outline-none"
            required 
          />
        </div>

        <!-- Password Input -->
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <i data-lucide="lock" class="w-4 h-4 text-gray-500"></i>
          </div>
          <input 
            type="password" 
            name="password" 
            placeholder="Password" 
            class="w-full pl-12 pr-4 py-3 bg-[#f5f5f5] text-gray-800 placeholder-gray-400 text-xs font-semibold rounded-xl border-none focus:ring-2 focus:ring-[#ff883e] focus:outline-none"
            required 
          />
        </div>

        <!-- Login Button -->
        <button 
          type="submit" 
          class="w-full mt-2 bg-[#f48545] hover:bg-[#e67a3b] text-white py-3 rounded-xl text-[11px] font-bold tracking-wider uppercase transition-colors duration-200">
          LOGIN
        </button>

        <!-- Register Link -->
        <p class="text-center text-xs text-gray-500 font-semibold mt-4">
          Don't have an account? <a href="/register" class="text-[#f48545] hover:text-[#e67a3b] ml-1 uppercase">Register</a>
        </p>
      </form>
    </div>

    <!-- Right Side (Big Logo) -->
    <div class="hidden md:flex items-center justify-center p-12 lg:p-16 w-full md:w-1/2">
      <!-- Stusion Logo Image -->
      <div class="w-full max-w-[280px] aspect-square flex items-center justify-center">
          <img src="{{ asset('assets/icons/stusion_logo.png') }}" alt="Stusion Logo" class="w-full h-full object-contain">
      </div>
    </div>
    
  </div>
</div>
@endsection
