<?php

use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\Post\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return to_route('posts.index');
})->name('home');

Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('posts.index');
    Route::get('/create', [PostController::class, 'create'])->name('posts.create');
    Route::get('/{post}', [PostController::class, 'show'])->name('posts.show');

    Route::middleware(['auth'])->group(function () {
        Route::get('/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::post('/', [PostController::class, 'store'])->name('posts.store');
        Route::put('/{post}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('posts.delete');

        Route::post('/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    });
});

Route::prefix('comments')->middleware(['auth'])->group(function () {
    Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('comments.delete');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
