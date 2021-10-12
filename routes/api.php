<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ShopCategoriesController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GroupController;
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

    //category
    Route::post('category/add', [CategoryController::class, 'store']);
    Route::post('category/update/{id}', [CategoryController::class, 'update']);
    Route::delete('category/delete/{id}', [CategoryController::class, 'destroy']);
    Route::get('category',[CategoryController::class, 'index']);

    //group
    Route::post('group/add', [GroupController::class, 'store']);
    Route::post('group/update/{id}', [GroupController::class, 'update']);
    Route::delete('group/delete/{id}', [GroupController::class, 'destroy']);
    Route::get('group',[GroupController::class, 'index']);
}); 


//Shop Categories
Route::post('shop_category/all', [ShopCategoriesController::class, 'show']);

// Register Shop and Owner
Route::post('shop/register', [ShopController::class, 'register']);
Route::get('send_email', [ShopController::class, 'send_email']);
