<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Organization;
use App\Policies\OrganizationPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Organization::class => OrganizationPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
