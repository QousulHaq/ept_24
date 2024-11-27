<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\PasswordResetResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::loginView(static fn() => view('auth.login'));
        Fortify::resetPasswordView(static function ($request) {
            return view('auth.passwords.reset', ['request' => $request]);
        });
        Fortify::ignoreRoutes();

        // replace original fortify action to role behavior
        $this->app->singleton(LoginResponseContract::class,
            fn() => !request()?->expectsJson() && request()?->user()?->isNotA('student')
                ? redirect()->intended(route('back-office.dashboard'))
                : redirect()->intended('/client'));

        RateLimiter::for('login', static function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email') . ':' . $request->ip());
        });

        RateLimiter::for('two-factor', static function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
