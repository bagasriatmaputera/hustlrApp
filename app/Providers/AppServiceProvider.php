<?php

namespace App\Providers;

use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use App\Repositories\Eloquent\JobApplicationRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            JobApplicationRepositoryInterface::class,
            JobApplicationRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        $this->schemeHttpsLive();

        // Owner has unrestricted super-admin access across all permissions and gates
        Gate::before(function ($user, string $ability): ?bool {
            try {
                if ($user->hasRole('Owner Integrated Personal System')) {
                    return true;
                }
            } catch (\Throwable $e) {
                // Ignore if role not seeded yet in some environments
            }

            return null;
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function schemeHttpsLive(): void
    {
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }
}
