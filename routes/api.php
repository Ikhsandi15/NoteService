<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\CategoryController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix("/v1")->group(function () {
    Route::prefix('/auth')->controller(AuthController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::post('/logout', 'logout')->middleware('auth:sanctum');
        Route::get('/forgot-password', 'forgotPassword');
    });

    Route::prefix('/categories')->middleware('auth:sanctum')->controller(CategoryController::class)->group(function () {
        Route::get('/', 'categoryLists');
        Route::post('/', 'create');
        Route::put('/{category_id}', 'update');
        Route::get('/search', 'search');
    });

    Route::prefix('/notes')->middleware('auth:sanctum')->controller(NoteController::class)->group(function () {
        Route::get('/', 'noteLists');
        Route::get('/{note_id}/detail', 'detail');
        Route::post('/', 'create');
        Route::post('/{note_id}/update', 'update');
        Route::delete('/{note_id}', 'delete');
        Route::post('/{note_id}/favorite', 'toggleFavorite');
        Route::get('/favorites', 'favoriteLists');
        Route::get('/search', 'search');
    });
});
