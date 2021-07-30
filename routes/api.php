<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\ApiController;
use App\Http\Controllers\ProductController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('login', [ApiController::class, 'authenticate']);
Route::post('register', [ApiController::class, 'register']);

Route::group([
    'prefix'=>'api/v1',
    'middleware' => ['jwt.verify']], 
    function() {
        Route::get('logout', [ApiController::class, 'logout']);
        Route::get('profile', [ApiController::class, 'user_profile']);
    }
);
Route::group([
    'prefix'=>'api/v1/admin',
    'middleware' => ['admin']], 
    function() {
        Route::get('logout', [ApiController::class, 'logout']);
        Route::get('list_user', [ApiController::class, 'admin_get_users']);
    }
);