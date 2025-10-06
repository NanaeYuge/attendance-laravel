<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Responses\CustomLoginResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, CustomLoginResponse::class);
    }

    public function boot(): void
    {

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(30)->by((string)$request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
        $email = (string) $request->input('email');
        $ip    = $request->ip();

        $key = $email !== ''
        ? 'login:' . Str::lower($email) . '|' . $ip
        : 'login:ip|' . $ip;

        return Limit::perMinute(30)->by($key);
        });


        Fortify::loginView(fn () => view('staff.auth.login'));
        Fortify::registerView(fn () => view('staff.auth.register'));
        Fortify::verifyEmailView(fn () => view('auth.verify-email'));

        Fortify::redirects('login', '/attendance');
        Fortify::redirects('logout', '/login');
        Fortify::redirects('register', '/attendance');

    }
}
