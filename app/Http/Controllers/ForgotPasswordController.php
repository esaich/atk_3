<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController
{
    /**
     * Tampilkan form input email.
     */
    public function showRequestForm()
    {
        return view('login.forgot-password');
    }

    /**
     * Generate OTP, simpan ke DB, dan kirim ke email user.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $this->generateAndSendOtp($request->email);

        // Selalu simpan email yang diinput & redirect ke halaman OTP yang sama,
        // baik email valid maupun tidak — supaya attacker tidak bisa
        // membedakan lewat behavior redirect, bukan cuma lewat teks pesan.
        session(['otp_email' => $request->email]);

        return redirect()->route('password.otp.form')
            ->with('status', 'Jika email terdaftar, kode OTP telah dikirim.');
    }

    /**
     * Kirim ulang OTP menggunakan email yang sudah tersimpan di session,
     * tanpa memaksa user mengetik ulang emailnya.
     */
    public function resendOtp()
    {
        $email = session('otp_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        $this->generateAndSendOtp($email);

        return redirect()->route('password.otp.form')
            ->with('status', 'Jika email terdaftar, kode OTP baru telah dikirim.');
    }

    /**
     * Helper: generate OTP baru & kirim ke email jika user memang ada.
     * Tidak melakukan apa pun (secara diam-diam) kalau email tidak ditemukan,
     * supaya keberadaan/ketidakberadaan user tidak bisa dibedakan pemanggil.
     */
    private function generateAndSendOtp(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return;
        }

        DB::table('password_otps')->where('email', $user->email)->delete();

        $otp = (string) random_int(100000, 999999);

        DB::table('password_otps')->insert([
            'email' => $user->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        Mail::to($user->email)->send(new OtpMail($otp));
    }

    /**
     * Tampilkan form input OTP.
     */
    public function showOtpForm()
    {
        if (! session('otp_email')) {
            return redirect()->route('password.request');
        }

        return view('login.verify-otp');
    }

    /**
     * Verifikasi OTP yang diinput user.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $email = session('otp_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        $record = DB::table('password_otps')
            ->where('email', $email)
            ->where('otp', $request->otp)
            ->first();

        if (! $record) {
            return back()->withErrors('Kode OTP salah.');
        }

        if (now()->greaterThan($record->expires_at)) {
            return back()->withErrors('Kode OTP sudah kedaluwarsa. Silakan minta kode baru.');
        }

        // Tandai OTP sudah diverifikasi lewat session, dipakai sebagai
        // "izin" buat akses halaman reset password.
        session(['otp_verified' => true]);

        // OTP langsung dihapus setelah dipakai — tidak dibiarkan "hidup"
        // sampai proses reset password selesai.
        DB::table('password_otps')->where('email', $email)->delete();

        return redirect()->route('password.reset.form');
    }

    /**
     * Tampilkan form password baru.
     */
    public function showResetForm()
    {
        if (! session('otp_email') || ! session('otp_verified')) {
            return redirect()->route('password.request');
        }

        return view('login.reset-password');
    }

    /**
     * Update password user dan bersihkan sesi OTP.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $email = session('otp_email');

        if (! $email || ! session('otp_verified')) {
            return redirect()->route('password.request');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('password.request')->withErrors('Terjadi kesalahan, silakan ulangi.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        session()->forget(['otp_email', 'otp_verified']);

        return redirect()->route('login')->with('status', 'Password berhasil diubah, silakan login.');
    }
}