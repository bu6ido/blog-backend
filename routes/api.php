<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->apiResource('posts', PostController::class);

/*Route::middleware(['auth:sanctum'])->controller(PostController::class)->group(function () {
    Route::get('/posts', 'index'); 
    Route::get('/posts/{post}', 'show');   
    Route::post('/posts', 'store');
    Route::put('/posts/{post}', 'update');
    Route::delete('/posts/{post}', 'destroy');
});*/

Route::middleware(['auth:sanctum'])->apiResource('posts.comments', CommentController::class)->shallow();

/*Route::middleware(['auth:sanctum'])->controller(CommentController::class)->group(function () {
    Route::get('/comments', 'index'); 
    Route::get('/comments/{comment}', 'show');   
    Route::post('/comments', 'store');
    Route::put('/comments/{comment}', 'update');
    Route::delete('/comments/{comment}', 'destroy');
});*/
