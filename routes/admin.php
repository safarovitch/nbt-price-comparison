<?php

use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here are all the admin-related routes for managing organizations and
| products. These routes are protected by auth and verified middleware.
|
*/

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Organization management
    Route::resource('organizations', OrganizationController::class);
    Route::post('organizations/{organization}/sync', [OrganizationController::class, 'sync'])->name('organizations.sync');

    // Product management (nested under organizations)
    Route::resource('organizations.products', ProductController::class)->except(['show']);

    // News Post management
    Route::resource('news', \App\Http\Controllers\Admin\NewsController::class);
    Route::post('upload/image', [\App\Http\Controllers\Admin\UploadController::class, 'image'])->name('upload.image');

    // Comment management
    Route::resource('comments', \App\Http\Controllers\Admin\CommentController::class)->only(['index', 'update', 'destroy']);
});
