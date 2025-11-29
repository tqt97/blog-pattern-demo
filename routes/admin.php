<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;

Route::resource('categories', CategoryController::class);
Route::resource('tags', TagController::class)->except('show');
