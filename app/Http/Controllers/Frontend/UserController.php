<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Socialite;
use Illuminate\Http\Request;
use Auth;

class UserController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        $user_social = Socialite::driver($provider)->user();
        $user = User::updateOrCreate(
            ['email' => $user_social->email],
            [
                $provider . '_id' => $user_social->id,
                'name' => $user_social->name,
                $provider. '_token' => $user_social->token
            ]);
        Auth::login($user);
        return redirect('/');
    }

}
