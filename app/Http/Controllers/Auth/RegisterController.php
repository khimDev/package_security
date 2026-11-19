<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Menampilkan halaman registrasi / buat akun
    public function showRegister()
    {
        return view('auth.register');
    }

    // Memproses pembuatan akun satpam baru
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'unique:users,username',
                'regex:/^[a-zA-Z0-9_]+$/',
            ],
            'password' => [
                'required',
                'string',
                'min:4',
                'confirmed',
            ],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.unique' => 'Username ini sudah digunakan, silakan pilih yang lain.',
            'username.regex' => 'Username hanya boleh huruf, angka, dan underscore (_).',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 4 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Buat akun baru (nama otomatis menggunakan huruf besar dari username)
        $user = User::create([
            'name' => ucfirst($validated['username']),
            'username' => strtolower($validated['username']),
            'password' => Hash::make($validated['password']),
        ]);

        // Langsung login otomatis setelah daftar
        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/')
            ->with('succes', 'Akun berhasil dibuat! Selamat datang di Package Security.');
    }
}
