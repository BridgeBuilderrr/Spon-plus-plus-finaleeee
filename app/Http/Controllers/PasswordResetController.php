<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    // ── Step 1: Tampilkan form email ──────────────────────────────
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // ── Step 2: Kirim OTP ke email ────────────────────────────────
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $otp = rand(100000, 999999);

        DB::table('password_reset_otps')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp'        => bcrypt($otp),
                'attempts'   => 0,
                'expires_at' => now()->addMinutes(10),
                'updated_at' => now(),
            ]
        );

        try {
            Mail::to($request->email)->send(new OtpMail($otp));
        } catch (\Exception $e) {
            logger()->error('Failed to send OTP email: ' . $e->getMessage());
            DB::table('password_reset_otps')->where('email', $request->email)->delete();
            return back()->withInput()->withErrors(['email' => 'Gagal mengirim email OTP. Silakan periksa koneksi internet/mail server Anda atau coba lagi nanti.']);
        }

        // Simpan email di session untuk dipakai di step berikutnya
        session(['otp_email' => $request->email]);

        return redirect()->route('password.verifyOtp')
            ->with('success', 'Kode OTP telah dikirim ke email Anda.');
    }

    // ── Step 3: Tampilkan form input OTP ──────────────────────────
    public function showVerifyForm()
    {
        if (!session('otp_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp');
    }

    // ── Step 4: Verifikasi OTP ────────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $email  = session('otp_email');
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan minta OTP baru.']);
        }

        $record = DB::table('password_reset_otps')->where('email', $email)->first();

        // Cek ada & belum expired
        if (!$record || now()->gt(\Carbon\Carbon::parse($record->expires_at))) {
            return back()->withErrors(['otp' => 'OTP tidak valid atau sudah kadaluarsa.']);
        }

        // Batasi max 5 percobaan
        if ($record->attempts >= 5) {
            DB::table('password_reset_otps')->where('email', $email)->delete();
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Terlalu banyak percobaan salah. Silakan minta OTP baru.']);
        }

        // Cek OTP salah
        if (!Hash::check($request->otp, $record->otp)) {
            $newAttempts = $record->attempts + 1;
            
            if ($newAttempts >= 5) {
                DB::table('password_reset_otps')->where('email', $email)->delete();
                return redirect()->route('password.request')
                    ->withErrors(['email' => 'Terlalu banyak percobaan salah. Silakan minta OTP baru.']);
            }

            DB::table('password_reset_otps')
                ->where('email', $email)
                ->update(['attempts' => $newAttempts]);

            return back()->withErrors(['otp' => 'Kode OTP salah. Sisa percobaan: ' . (5 - $newAttempts)]);
        }

        // OTP benar → hapus record & tandai session verified
        DB::table('password_reset_otps')->where('email', $email)->delete();
        session(['otp_verified' => true]);

        return redirect()->route('password.resetForm');
    }

    // ── Step 5: Tampilkan form password baru ──────────────────────
    public function showResetForm()
    {
        if (!session('otp_verified')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password');
    }

    // ── Step 6: Simpan password baru ─────────────────────────────
    public function resetPassword(Request $request)
    {
        if (!session('otp_verified')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $email = session('otp_email');
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan minta OTP baru.']);
        }

        User::where('email', $email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Bersihkan session
        session()->forget(['otp_email', 'otp_verified']);

        return redirect()->route('login')
            ->with('success', 'Password berhasil diubah! Silakan login.');
    }

    // ── Step 7: Kirim ulang OTP (tanpa pindah page) ────────────────
    public function resendOtp()
    {
        $email = session('otp_email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        $otp = rand(100000, 999999);

        DB::table('password_reset_otps')->updateOrInsert(
            ['email' => $email],
            [
                'otp'        => bcrypt($otp),
                'attempts'   => 0,
                'expires_at' => now()->addMinutes(10),
                'updated_at' => now(),
            ]
        );

        try {
            Mail::to($email)->send(new OtpMail($otp));
        } catch (\Exception $e) {
            logger()->error('Failed to resend OTP email: ' . $e->getMessage());
            return back()->withErrors(['otp' => 'Gagal mengirim email OTP. Silakan periksa koneksi internet/mail server Anda atau coba lagi nanti.']);
        }

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}
