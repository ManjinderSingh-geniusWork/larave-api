<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Storage;
use App\Events\EmailVerfication;


class ApiController extends Controller
{
    public function register(Request $request)
    {
    	//Validate data
        $data = $request->only('user_name', 'email', 'password');
        $validator = Validator::make($data, [
            'email' => 'required|email|unique:users',
            'user_name'=>'required|unique:users|min:4|max:20',
            // 'avtar' => 'mimes:jpeg,jpg,png,gif',
            // 'avtar' => 'dimensions:min_width=256,min_height=256,max_width=256,max_height=256',
 
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $userData = array(
            'user_name'=>$request->user_name,
        	'email' => $request->email,
            'role' => 'user',
        	'password' => bcrypt($request->password)
        );
        if($request->hasFile('avtar')){
            $filename = $request->avtar->getClientOriginalName();
            $request->avtar->storeAs('user_avtars',$filename,'public');
            $userData['avtar'] = $filename;
        }
        //Request is valid, create new user
        $user = User::create($userData);
        $user->email_verified = 0;

        // send email verification code
        event(new EmailVerfication($user));

        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }
 
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                	'success' => false,
                	'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
    	return $credentials;
            return response()->json([
                	'success' => false,
                	'message' => 'Could not create token.',
                ], 500);
        }
 	
 		//Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }
 
    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

		//Request is validated, do logout        
        try {
            JWTAuth::invalidate($request->token);
 
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_user(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();       
        // $user = JWTAuth::authenticate($request->token);
         return response()->json(['success' => true,'user' => $user]);
    }


    public function porfile_edit(Request $request){

        //validator place
        $user = JWTAuth::parseToken()->authenticate();     
        $users = User::find($user->id);
        if($users->email_verified == 0){
            return response()->json([
                'success' => false,
                'message' => 'Please verify Email.',
            ], 200);
        }
        $data = $request->only('name','user_name', 'avtar');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'user_name'=>'required |unique:users| min:4 |max:20',
            'avtar' => 'mimes:jpeg,jpg,png,gif',
            // 'avtar' => 'dimensions:min_width=256,min_height=256,max_width=256,max_height=256',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }


        $users = User::find($user->id);
        $users->name = $request->name;
        $users->user_name = $request->user_name;

        // $users->password = bcrypt($request->password);


        if($request->hasFile('avtar')){
            $filename = $request->avtar->getClientOriginalName();
            $request->avtar->storeAs('user_avtars',$filename,'public');
            $users->avtar = $filename;
        }
        $users->save();

        $data[] = [
            'id'=>$users->id,
            'name'=>$users->name,
            'user_name'=>$users->user_name,
            'avtar'=>Storage::url($users->avtar),
            'status'=>200,
        ];
        return response()->json($data);

    }
    public function verify_email(Request $request){

        //validator place
            $user = JWTAuth::parseToken()->authenticate();     
            
            $data = $request->only('verify_code');
            $validator = Validator::make($data, [
                'verify_code' => 'required|numeric|digits:6',
            ]);
                      //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 200);
            }
            $users = User::find($user->id);
            if($users->verification_code == $request->verify_code && $users->email_verified == 0){
                $users->email_verified = 1;
                $users->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Email verified sucessfully'
                ]);
            }else if( $users->email_verified == 1){
                return response()->json([
                    'success' => false,
                    'message' => 'Email already verified sucessfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Email verification failed'
            ]);
    
        }


    
}
