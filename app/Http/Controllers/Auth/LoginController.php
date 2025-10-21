<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    

    public function index()
    {
        return view('auth.loginaja');
    }

    public function loginvalidate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email anda tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password anda tidak boleh kosong',
        ]);

        // Cek apakah user mencoba login terlalu sering
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Redirect ke halaman yang diminta sebelumnya atau ke dashboard
            return redirect()->intended('/')->with('success', 'Login berhasil! Selamat datang kembali.');
        }

        // Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Anda telah berhasil logout.');
    }
}