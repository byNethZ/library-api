<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NotifyController;
use App\Http\Controllers\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [UserController::class, 'login']);

Route::post('/register', [UserController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    //Route::post('/products', [ProductController::class, 'store']);
    Route::post('/notify', [NotifyController::class, 'store']);

    Route::get('/all/authors', [AuthorController::class, 'all']);
    Route::get('/all/categories', [CategoryController::class, 'all']);

    Route::post('/borrow/{id}/{status}', [BookController::class, 'borrow']);

    Route::post('/logout', [UserController::class, 'logout']);

    Route::resource('/users', UserController::class);
    Route::resource('/books', BookController::class);
    Route::resource('/categories', CategoryController::class);
    Route::resource('/authors', AuthorController::class);



});
