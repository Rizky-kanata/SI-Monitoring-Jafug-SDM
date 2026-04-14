<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'username' => preg_replace('/\s+/', ' ', trim((string) $request->input('username', ''))),
        ]);

        $validated = $request->validate(
            [
                'nickname' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+(?: [A-Za-z0-9_-]+)*$/', 'unique:users,username'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ],
            [
                'nickname.required' => 'Nickname wajib diisi.',
                'nickname.max' => 'Nickname maksimal 255 karakter.',
                'username.required' => 'Username wajib diisi.',
                'username.regex' => 'Username cuma boleh berisi huruf, angka, spasi, tanda minus (-), dan underscore (_).',
                'username.max' => 'Username maksimal 50 karakter.',
                'username.unique' => 'Username ini sudah dipakai, coba yang lain.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email belum valid.',
                'email.max' => 'Email maksimal 255 karakter.',
                'email.unique' => 'Email ini sudah terdaftar.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password belum sama.',
            ]
        );

        $user = User::create([
            'name' => $validated['nickname'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Registrasi berhasil. Selamat datang di Monitoring Kepangkatan SDM.');
    }
}
