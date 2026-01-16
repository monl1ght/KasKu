<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\PendingRegistration;
use App\Notifications\PendingEmailVerificationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // normalisasi email
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        // validasi dasar (email + name) dulu biar bisa cek pending
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'max:255',
                'email:rfc,dns',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $email = strtolower(trim((string) $value));
                    $domain = str_contains($email, '@') ? substr(strrchr($email, '@'), 1) : '';
                    $allowed = ['gmail.com', 'googlemail.com'];
                    if ($domain && ! in_array($domain, $allowed, true)) {
                        $fail('Hanya email Gmail (@gmail.com) yang diperbolehkan.');
                    }
                },
            ],
        ]);

        $email = (string) $request->email;

        // 1) kalau sudah jadi user di tabel users → suruh login
        if (User::where('email', $email)->exists()) {
            return back()
                ->withErrors(['email' => 'Email ini sudah terdaftar. Silakan login.'])
                ->withInput();
        }

        // 2) kalau sudah pending → resend verifikasi (tanpa error)
        $pending = PendingRegistration::where('email', $email)->first();
        if ($pending) {
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

            $request->session()->put('pending_email', $pending->email);

            return redirect()->route('verification.notice')
                ->with('status', 'verification-link-sent');
        }

        // 3) baru validasi password untuk pendaftaran baru
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // create pending baru
        $token = Str::random(64);
        $expiresAt = now()->addMinutes(60);

        $pending = PendingRegistration::create([
            'name' => (string) $request->name,
            'email' => $email,
            'password' => Hash::make((string) $request->password),
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

        $request->session()->put('pending_email', $pending->email);

        return redirect()->route('verification.notice');
    }


    /**
     * List minimal domain disposable (boleh kamu tambah).
     */
    private function disposableEmailDomains(): array
    {
        return [
            'mailinator.com',
            'guerrillamail.com',
            '10minutemail.com',
            'tempmail.com',
            'temp-mail.org',
            'yopmail.com',
            'getnada.com',
            'dispostable.com',
            'sharklasers.com',
            'throwawaymail.com',
        ];
    }
}
