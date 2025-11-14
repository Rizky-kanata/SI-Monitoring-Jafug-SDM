<?php

namespace App\Http\Controllers;

use App\Jobs\SendPasswordOtpEmail;
use App\Models\PasswordOtp;
use App\Models\User;
use App\Services\BrevoMailer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class PasswordResetController extends Controller
{
    public function showRequest(): View
    {
        return view('auth.passwords.forgot');
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'exists:users,username'],
        ], [
            'username.exists' => 'Username tidak ditemukan.',
        ]);

        /** @var User $user */
        $user = User::where('username', $validated['username'])->firstOrFail();

        if (! $user->email) {
            $fallbackEmail = config('services.password_reset.fallback_email');

            if (! $fallbackEmail) {
                return back()
                    ->withInput()
                    ->withErrors(['username' => 'Akun ini belum memiliki email terdaftar. Hubungi administrator.']);
            }

            $user->forceFill([
                'email' => $fallbackEmail,
            ])->save();
        }

        PasswordOtp::where('user_id', $user->id)->delete();

        $token = (string) Str::uuid();
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordOtp::create([
            'user_id' => $user->id,
            'token' => $token,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
            'max_attempts' => 5,
        ]);

        try {
            app(BrevoMailer::class)->assertSenderVerified((string) config('mail.from.address'));
        } catch (Throwable $exception) {
            Log::error('Validasi pengirim Brevo gagal.', [
                'message' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['username' => 'Konfigurasi email belum siap: ' . $exception->getMessage()]);
        }

        SendPasswordOtpEmail::dispatch(
            $user->email,
            $user->name ?? $user->username,
            $code
        );

        return redirect()
            ->route('password.verify', $token)
            ->with('status', 'Kode OTP telah dikirim ke email terdaftar.');
    }

    public function showVerify(string $token): RedirectResponse|View
    {
        $otp = PasswordOtp::where('token', $token)->first();

        if (! $otp) {
            return redirect()
                ->route('password.request')
                ->withErrors(['username' => 'Permintaan OTP tidak ditemukan. Silakan buat permintaan baru.']);
        }

        if ($otp->isExpired()) {
            $otp->delete();

            return redirect()
                ->route('password.request')
                ->withErrors(['username' => 'Kode OTP sudah kadaluarsa. Silakan buat permintaan baru.']);
        }

        return view('auth.passwords.verify', [
            'token' => $token,
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'uuid'],
            'code' => ['required', 'regex:/^[0-9]{6}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'code.regex' => 'Kode OTP harus berisi 6 digit angka.',
        ]);

        $otp = PasswordOtp::where('token', $validated['token'])->first();

        if (! $otp) {
            return redirect()
                ->route('password.request')
                ->withErrors(['username' => 'Permintaan OTP tidak ditemukan.']);
        }

        if ($otp->isExpired()) {
            $otp->delete();

            return redirect()
                ->route('password.request')
                ->withErrors(['username' => 'Kode OTP sudah kadaluarsa. Silakan minta kode baru.']);
        }

        if (! $otp->hasAttemptsRemaining()) {
            $otp->delete();

            return redirect()
                ->route('password.request')
                ->withErrors(['username' => 'Batas percobaan OTP terlampaui. Silakan minta kode baru.']);
        }

        if (! Hash::check($validated['code'], $otp->code)) {
            $otp->increment('attempts');

            return back()
                ->withInput(['token' => $otp->token])
                ->withErrors(['code' => 'Kode OTP tidak valid.']);
        }

        $user = $otp->user;

        if (! $user) {
            $otp->delete();

            return redirect()
                ->route('password.request')
                ->withErrors(['username' => 'Pengguna tidak ditemukan. Silakan hubungi administrator.']);
        }

        $user->forceFill([
            'password' => $validated['password'],
        ])->save();

        $otp->delete();

        return redirect()
            ->route('login')
            ->with('status', 'Password berhasil diperbarui. Silakan login dengan password baru.');
    }
}
