<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ShopCategoriesController;
use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Users
Route::post('register/employee', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::group(['middleware' => 'auth:api'], function(){
    Route::get('user/detail', [UserController::class, 'details']);
    Route::post('logout', [UserController::class, 'logout']);
}); 


//Shop Categories
Route::post('shop_category/all', [ShopCategoriesController::class, 'show']);

// Register Shop and Owner
Route::post('register/shop', [ShopController::class, 'register']);
