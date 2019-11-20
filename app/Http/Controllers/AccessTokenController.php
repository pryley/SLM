<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class AccessTokenController extends Controller
{
    /**
     * Passport doesn't restrict a client requesting any scope so we have to restrict it.
     * http://stackoverflow.com/questions/39436509/laravel-passport-scopes.
     *
     * @return \Illuminate\Http\Response
     */
    public function createAccessToken(Request $request)
    {
        if ($request->input('username') && 'password' == $request->input('grant_type')) {
            $user = app(User::class)->where(['email' => $request->input('username')])->first();
        }
        if (!($user instanceof User) || User::BASIC_ROLE === $user->role) {
            $request->scope = 'basic';
        }
        return app()->dispatch(
            $request->create('/oauth/token', 'post', $request->all())
        );
    }
}
