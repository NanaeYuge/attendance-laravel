<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user && property_exists($user, 'is_admin') && $user->is_admin) {

            return redirect()->intended('/admin/attendance');

        }

        return redirect()->intended('/attendance');
    }
}
