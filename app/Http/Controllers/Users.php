<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;

class Users extends Controller
{
    //
     //
    public function register(){
        die('front-register');
    }
    public function verify_email(Request $request): RedirectResponse
    {
        $user = User::find($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            die('Email-already-verified');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        die('Email-verified-succes');
    }
}
