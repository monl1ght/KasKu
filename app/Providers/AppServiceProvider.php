<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

use App\Models\Organization;
use App\Models\PembayaranKas;              // ✅ tambah
use App\Observers\PembayaranKasObserver;   // ✅ tambah

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ OBSERVER (log aktivitas pembayaran admin)
        PembayaranKas::observe(PembayaranKasObserver::class);

        /*
         * Gate: manageMembers
         */
        Gate::define('manageMembers', function ($user, ?Organization $org) {
            if (config('app.debug')) {
                Log::debug('Gate manageMembers called', [
                    'user_id' => $user->id ?? null,
                    'org_id'  => $org->id ?? null,
                ]);
            }

            if (! $org || ! $org->id) {
                if (config('app.debug')) {
                    Log::debug('manageMembers: no org provided');
                }
                return false;
            }

            // ambil membership (pivot)
            $membership = $user->organizations()
                ->where('organizations.id', $org->id)
                ->first();

            if (config('app.debug')) {
                Log::debug('manageMembers: membership', [
                    'found' => $membership ? true : false,
                    'pivot_role' => optional($membership)->pivot->role,
                ]);
            }

            if (! $membership) {
                return false;
            }

            // daftar role yang diizinkan (semua lowercase)
            $allowedRoles = [
                'admin',
                'bendahara',
                'treasurer',
            ];

            $role = is_string(optional($membership->pivot)->role)
                ? strtolower(trim($membership->pivot->role))
                : null;

            $allowed = in_array($role, $allowedRoles, true);

            if (config('app.debug')) {
                Log::debug('manageMembers: result', ['role' => $role, 'allowed' => $allowed]);
            }

            return $allowed;
        });
    }
}
