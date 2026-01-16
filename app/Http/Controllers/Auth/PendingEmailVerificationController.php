<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PendingRegistration;
use App\Models\User;
use App\Notifications\PendingEmailVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class PendingEmailVerificationController extends Controller
{
    public function notice(Request $request)
    {
        // halaman "cek email kamu" (guest)
        $email = $request->session()->get('pending_email');

        return view('auth.verify-email', [
            'email' => $email,
        ]);
    }

    public function resend(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $email = strtolower(trim((string) $request->input('email')));

        $pending = PendingRegistration::where('email', $email)->first();

        // demi keamanan: kalau tidak ada, tetap balas sukses (jangan bocorkan email ada/tidak)
        if (! $pending) {
            return back()->with('status', 'verification-link-sent');
        }

        // buat token baru
        $token = Str::random(64);
        $expiresAt = now()->addMinutes(60);

        $pending->update([
            'token_hash' => hash('sha256', $token),
            'expires_at' => $expiresAt,
        ]);

        Notification::route('mail', $pending->email)
            ->notify(new PendingEmailVerificationNotification(
                pendingId: $pending->id,
                name: $pending->name,
                email: $pending->email,
                token: $token,
                expiresAt: $expiresAt,
            ));

        return back()->with('status', 'verification-link-sent');
    }

    public function verify(Request $request, PendingRegistration $pending, string $token)
    {
        // pastikan URL valid (signed middleware sudah handle juga, ini extra safety)
        if (! $request->hasValidSignature()) {
            abort(403, 'Link verifikasi tidak valid / sudah diubah.');
        }

        if ($pending->expires_at->isPast()) {
            // pending expired → hapus biar bersih
            $pending->delete();
            return redirect()->route('register')
                ->withErrors(['email' => 'Link verifikasi sudah kedaluwarsa. Silakan daftar ulang.']);
        }

        // cek token
        $tokenHash = hash('sha256', $token);
        if (! hash_equals($pending->token_hash, $tokenHash)) {
            abort(403, 'Token verifikasi tidak valid.');
        }

        // kalau email sudah jadi user (misal klik 2x atau sudah dibuat)
        if (User::where('email', $pending->email)->exists()) {
            $pending->delete();
            return redirect()->route('login')
                ->with('status', 'Akun sudah aktif. Silakan login.');
        }

        // ✅ baru buat user di tabel users saat verifikasi sukses
        $user = User::create([
            'name' => $pending->name,
            'email' => $pending->email,
            'password' => $pending->password,
            'email_verified_at' => now(),
        ]);

        // hapus pending
        $pending->delete();

        // auto-login setelah verifikasi (opsional, tapi enak)
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
