<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PendingRegistration;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        // Kalau nanti kena InvalidStateException, ganti jadi:
        // $googleUser = Socialite::driver('google')->stateless()->user();
        $googleUser = Socialite::driver('google')->user();

        $email = strtolower(trim((string) $googleUser->getEmail()));
        $domain = str_contains($email, '@') ? substr(strrchr($email, '@'), 1) : '';

        // ✅ khusus Gmail saja
        if (!in_array($domain, ['gmail.com', 'googlemail.com'], true)) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Hanya akun Gmail (@gmail.com) yang diperbolehkan.']);
        }

        // Kalau sebelumnya email ini sempat pending register (menunggu verifikasi),
        // biar nggak nyangkut → hapus pending-nya
        PendingRegistration::where('email', $email)->delete();

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: 'User',
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(), // ✅ dianggap verified karena login via Google
                'password' => bcrypt(Str::random(32)),
            ]);
        } else {
            // link google_id kalau belum ada
            if (!$user->google_id) {
                $user->google_id = $googleUser->getId();
            }
            // anggap verified (optional, tapi enak biar konsisten)
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
            }
            $user->save();
        }

        Auth::login($user, true);

        return redirect()->intended(route('dashboard'));
    }
}
