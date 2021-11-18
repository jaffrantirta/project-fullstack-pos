<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ShopCategoriesController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ProductTaxController;
use App\Http\Controllers\BuyerTypeController;
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

    //product
    Route::post('product/add', [ProductCOntroller::class, 'store']);
    Route::post('product/update/{id}', [ProductCOntroller::class, 'update']);
    Route::delete('product/delete/{id}', [ProductCOntroller::class, 'destroy']);
    Route::get('product',[ProductCOntroller::class, 'index']);

    //product variant
    Route::post('product_variant/add', [ProductVariantController::class, 'store']);
    Route::post('product_variant/update/{id}', [ProductVariantController::class, 'update']);
    Route::delete('product_variant/delete/{id}', [ProductVariantController::class, 'destroy']);
    Route::get('product_variant',[ProductVariantController::class, 'index']);

    //product tax
    Route::post('product_tax/add', [ProductTaxController::class, 'store']);
    Route::post('product_tax/update/{product_id}', [ProductTaxController::class, 'update']);
    Route::delete('product_tax/delete/{product_id}', [ProductTaxController::class, 'destroy']);
    Route::get('product_tax',[ProductTaxController::class, 'index']);

    //buyer type
    Route::post('buyer_type/add', [BuyerTypeController::class, 'store']);
    Route::post('buyer_type/update/{product_id}', [BuyerTypeController::class, 'update']);
    Route::delete('buyer_type/delete/{product_id}', [BuyerTypeController::class, 'destroy']);
    Route::get('buyer_type',[BuyerTypeController::class, 'index']);
}); 


//Shop Categories
Route::get('shop_category/all', [ShopCategoriesController::class, 'show']);

// Register Shop and Owner
Route::post('shop/register', [ShopController::class, 'register']);
Route::get('send_email', [ShopController::class, 'send_email']);
