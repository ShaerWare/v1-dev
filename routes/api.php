<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeamLeadController;
use App\Http\Controllers\ProductController;

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
