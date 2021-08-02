<?php

namespace App\Listeners;

use App\Events\EmailVerfication;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use App\Mail\EmailVerificationCode;
use Illuminate\Support\Facades\Mail;

class SendEmailVerificationCode
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EmailVerfication  $event
     * @return void
     */
    public function handle(EmailVerfication $event)
    {
        //
        $userinfo = $event->user;
        $randomid = mt_rand(100000,999999);
        
        $users = User::find($userinfo->id);
        $users->verification_code = $randomid;
        $users->save();

        Mail::to($users->email)->send(new EmailVerificationCode($users));
    }
}
