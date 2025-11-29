<?php

use App\Exceptions\PostException;
use App\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth'])
                ->prefix('admin')
                ->name('admin.')
                // ->namespace('App\Http\Controllers\Admin')
                ->as('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 1. Chuyển PostException -> ValidationException
        $exceptions->map(PostException::class, function ($e) {
            // Auto detect field bị lỗi từ message, hoặc hard-code 'slug'
            $message = $e->getMessage();

            // Nếu message liên quan đến slug
            if (str_contains(strtolower($message), 'slug')) {
                throw ValidationException::withMessages([
                    'slug' => $message,
                ]);
            }

            // Fallback: các lỗi khác
            throw ValidationException::withMessages([
                'error' => $message,
            ]);
        });

        // 2. RepositoryException → trả lỗi 500 hoặc custom message
        $exceptions->map(RepositoryException::class, function ($e) {
            throw new HttpException(
                500,
                $e->getMessage(),
            );
        });

        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
            $class = $model::class;

            info("Attempted to lazy load [{$relation}] on model [{$class}].");
        });
    })->create();
