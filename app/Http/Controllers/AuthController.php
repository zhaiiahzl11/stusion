<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Counselor;
use App\Models\Student;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        $credentials = [
            'name' => $request->input('username'),
            'password' => $request->input('password')
        ];

        // Try Admin
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        // Try Counselor
        if (Auth::guard('counselor')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('counselor.dashboard');
        }

        // Try Student
        if (Auth::guard('student')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('student.dashboard');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('admin')->check()) { Auth::guard('admin')->logout(); }
        if (Auth::guard('counselor')->check()) { Auth::guard('counselor')->logout(); }
        if (Auth::guard('student')->check()) { Auth::guard('student')->logout(); }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function storeRegister(Request $request)
    {
        $request->validate([
            'role' => 'required|in:admin,counselor,student',
            'username' => 'required|string|unique:admins,name|unique:counselors,name|unique:students,name',
            'password' => 'required|min:6'
        ]);

        $role = $request->input('role');
        $data = [
            'name' => $request->input('username'),
            'email' => $request->input('username'),
            'password' => Hash::make($request->input('password'))
        ];

        if ($role === 'admin') {
            $data['status'] = 'Active';
            $user = Admin::create($data);
        } elseif ($role === 'counselor') {
            $user = Counselor::create($data);
        } else {
            $user = Student::create($data);
        }

        Auth::guard($role)->login($user);

        return redirect()->route($role . '.dashboard');
    }
}
