<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminControllerWeb;
use App\Http\Controllers\TeamLeadControllerWeb;
use App\Http\Controllers\ProductControllerWeb;

Route::get('/swagger', function () {
    return view('index');
});

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('roles', [AdminControllerWeb::class, 'indexRoles'])->name('roles.index');
    Route::get('roles/create', [AdminControllerWeb::class, 'createRole'])->name('roles.create');
    Route::post('roles', [AdminControllerWeb::class, 'storeRole'])->name('roles.store');
    Route::get('roles/{id}/edit', [AdminControllerWeb::class, 'editRole'])->name('roles.edit');
    Route::put('roles/{id}', [AdminControllerWeb::class, 'updateRole'])->name('roles.update');
    Route::delete('roles/{id}', [AdminControllerWeb::class, 'destroyRole'])->name('roles.destroy');
    Route::get('users', [AdminControllerWeb::class, 'indexUsers'])->name('users.index');
    Route::post('users/{userId}/assign-role', [AdminControllerWeb::class, 'assignRole'])->name('users.assignRole');
    Route::post('users/{userId}/remove-role/{roleName}', [AdminControllerWeb::class, 'removeRole'])->name('users.removeRole');

});

// TeamLead Routes
Route::prefix('teamlead')->name('teamlead.')->group(function () {
    Route::get('buyers', [TeamLeadControllerWeb::class, 'indexBuyers'])->name('buyers.index');
    Route::post('buyers/{userId}/assign', [TeamLeadControllerWeb::class, 'assignBuyerRole'])->name('buyers.assign');
    Route::post('buyers/{userId}/remove', [TeamLeadControllerWeb::class, 'removeBuyerRole'])->name('buyers.remove');
});

// Product Routes
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductControllerWeb::class, 'index'])->name('index');
    Route::get('/create', [ProductControllerWeb::class, 'create'])->name('create');
    Route::post('/', [ProductControllerWeb::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ProductControllerWeb::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProductControllerWeb::class, 'update'])->name('update');
    Route::delete('/{id}', [ProductControllerWeb::class, 'destroy'])->name('destroy');
});

require __DIR__.'/auth.php';
