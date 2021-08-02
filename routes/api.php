<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\ApiController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MailController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [ApiController::class, 'authenticate']);
Route::post('register', [ApiController::class, 'register']);

// API Routes for customers/users
Route::middleware(['jwt.verify'])->group(
    function() {
        Route::get('logout', [ApiController::class, 'logout']);
        Route::get('get_user', [ApiController::class, 'get_user']);
        Route::post('update-profile', [ApiController::class, 'porfile_edit']);
        Route::post('email/verify', [ApiController::class, 'verify_email']);

    }
);

// API Routes for Admin
Route::middleware(['jwt.verify'])->prefix('admin')->group(
    function() {
        Route::get('logout', [ApiController::class, 'logout']);
        Route::get('list_user', [ApiController::class, 'admin_get_users']);
        Route::post('/mail/sign-up-invitation', [MailController::class, 'admin_invite_user']);
    }
);