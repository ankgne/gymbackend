<?php


namespace App\Http\Responses;


use App\Http\Resources\UserResource;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;

class LoginResponse implements LoginResponseContract
{

    public function toResponse($request)
    {
        return $request->wantsJson()
            ? new UserResource(auth()->user())
            : redirect()->intended(Fortify::redirects('login'));
    }
}
