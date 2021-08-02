<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\UserInvitation;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class MailController extends Controller
{
    //
    public function admin_invite_user(Request $request){

        $data = $request->only('email');
        $validator = Validator::make($data, [
            'email' => 'required|email|unique:users',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $res = Mail::to($request->email)->send(new UserInvitation());
        var_dump($res);die;
        if($res == 1){
            return response()->json(['success' => true,'user' => 'Email sent']);
        }
        return response()->json(['success' =>false,'message' => 'Email Failed']);


    }
}
