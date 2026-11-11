<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Global Str class alias for blade views & vendor packages
        if (!class_exists('Str')) {
            class_alias(\Illuminate\Support\Str::class, 'Str');
        }

        // Share active currencies with all views for the header ticker
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $activeCurrencies = \Illuminate\Support\Facades\Cache::remember('active_currencies', 3600, function () {
                return \App\Models\Currency::active()->get();
            });
            $view->with('activeCurrencies', $activeCurrencies);
        });

        // echo "Booting AppServiceProvider\n";
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            // Super-admin & admin bypass
            if (method_exists($user, 'hasRole') && ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('superadmin'))) {
                return true;
            }

            // Unified Operational Triad: strictly Consultant, Marketing, and Application roles have equal access
            $triadRoles = ['consultant', 'marketing', 'application'];
            $userHasTriadRole = false;
            if (method_exists($user, 'hasRole')) {
                foreach ($triadRoles as $r) {
                    if ($user->hasRole($r)) {
                        $userHasTriadRole = true;
                        break;
                    }
                }
            }

            // Support multiple abilities/roles separated by pipe |
            $abilities = explode('|', $ability);

            foreach ($abilities as $singleAbility) {
                $cleanAbility = ltrim($singleAbility, '*');

                // If user is part of the operational triad, grant access to triad role checks
                if ($userHasTriadRole) {
                    if (in_array($cleanAbility, ['consultant', 'marketing', 'application', 'invoice'], true)) {
                        return true;
                    }
                }

                // Check if Tyro has this privilege
                if (method_exists($user, 'hasPrivilege') && ($user->hasPrivilege($singleAbility) || $user->hasPrivilege($cleanAbility))) {
                    return true;
                }

                // Check if Tyro has this role (some checks might use role name as ability)
                if (method_exists($user, 'hasRole') && ($user->hasRole($singleAbility) || $user->hasRole($cleanAbility))) {
                    return true;
                }
            }

            return null; // Fallback to other gates/policies
        });

        // Override the vendor UserController with our local one
        $this->app->bind(
            \HasinHayder\TyroDashboard\Http\Controllers\UserController::class,
            \App\Http\Controllers\Admin\UserController::class
        );
    }
}
