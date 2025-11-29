<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::resource('posts', PostController::class);

Route::resource('blog', PostController::class)
    ->parameters(['blog' => 'post'])
    ->scoped(['post' => 'slug']);

// Route::prefix('admin')->name('admin.')->group(function () {
//     Route::resource('posts', AdminPostController::class);

//     // route publish riêng
//     Route::post('posts/{post}/publish', [AdminPostController::class, 'publish'])
//         ->name('posts.publish');

// Route::post('posts/{post}/publish', [AdminPostController::class, 'publish'])
//     ->name('posts.publish')
//     ->middleware('can:publish,post'); // <- check quyền tại route
// });
