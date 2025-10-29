<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        // For Inertia requests, force a hard client-side redirect so the Blade chrome mounts
        if ($request->header('X-Inertia')) {
            return Inertia::location('/dashboard');
        }

        // Fallback for non-Inertia requests
        return redirect()->intended('/dashboard');
    }
}


