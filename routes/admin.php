<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\TagController;

Route::resource('categories', CategoryController::class);
Route::resource('tags', TagController::class)->except('show');
Route::resource('posts', PostController::class)->except('show');
Route::post('posts/bulk', [PostController::class, 'bulk'])->name('posts.bulk');
