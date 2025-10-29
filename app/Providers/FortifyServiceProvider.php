<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use App\Http\Responses\LogoutResponse as CustomLogoutResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Http\Responses\LoginResponse as CustomLoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Override Fortify logout redirect
        $this->app->singleton(LogoutResponseContract::class, CustomLogoutResponse::class);
        // Force full reload after login so app chrome appears immediately
        $this->app->singleton(LoginResponseContract::class, CustomLoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);

        // Custom authentication with active status check
        Fortify::authenticateUsing(function (Request $request) {
            $user = \App\Models\User::where('email', $request->email)
                ->orWhere('username', $request->email)
                ->first();

            if ($user && \Hash::check($request->password, $user->password)) {
                if (!$user->is_active) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        Fortify::username() => ['Your account is inactive. Please contact administrator.'],
                    ]);
                }
                return $user;
            }

            return null;
        });
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn (Request $request) => Inertia::render('auth/Login', [
            'canResetPassword' => Features::enabled(Features::resetPasswords()),
            'canRegister' => Features::enabled(Features::registration()),
            'status' => $request->session()->get('status'),
        ]));

        Fortify::resetPasswordView(fn (Request $request) => Inertia::render('auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]));

        Fortify::requestPasswordResetLinkView(fn (Request $request) => Inertia::render('auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ]));

        Fortify::verifyEmailView(fn (Request $request) => Inertia::render('auth/VerifyEmail', [
            'status' => $request->session()->get('status'),
        ]));

        Fortify::registerView(fn () => Inertia::render('auth/Register'));

        Fortify::twoFactorChallengeView(fn () => Inertia::render('auth/TwoFactorChallenge'));

        Fortify::confirmPasswordView(fn () => Inertia::render('auth/ConfirmPassword'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
