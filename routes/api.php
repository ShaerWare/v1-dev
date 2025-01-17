<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeamLeadController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\AuthorizationController;
use Laravel\Passport\Http\Controllers\TransientTokenController;

Route::post('/oauth/token', [AccessTokenController::class, 'issueToken'])
    ->name('passport.token');

Route::get('/oauth/authorize', [AuthorizationController::class, 'authorize'])
    ->name('passport.authorizations');

Route::post('/oauth/token/refresh', [TransientTokenController::class, 'refresh'])
    ->name('passport.token.refresh');

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::middleware('auth:sanctum')->group(function () {
    // Администратор
    Route::post('roles', [AdminController::class, 'createRole']);
    Route::post('users/{userId}/roles', [AdminController::class, 'assignRoleToUser']);
    Route::post('roles/{roleId}/permissions', [AdminController::class, 'assignPermissionsToRole']);

    // Лидеры
    Route::post('users/{userId}/assign-buyer', [TeamLeadController::class, 'assignBuyerRole']);
    Route::post('users/{userId}/remove-buyer', [TeamLeadController::class, 'removeBuyerRole']);

    // Байеры
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'create']);
    Route::put('products/{productId}', [ProductController::class, 'update']);
    Route::delete('products/{productId}', [ProductController::class, 'destroy']);
});
