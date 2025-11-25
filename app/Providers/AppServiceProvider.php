<?php

namespace App\Providers;

use App\Cache\Adapters\DbNamespacedCacheStore;
use App\Cache\Adapters\RedisNamespacedCacheStore;
use App\Cache\Contracts\NamespacedCacheStoreInterface;
use App\Models\Post;
use App\Observers\PostObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NamespacedCacheStoreInterface::class, function ($app) {
            $default = config('cache.default');

            if (in_array($default, ['redis', 'memcached', 'dynamodb'], true)) {
                return new RedisNamespacedCacheStore;
            }

            return new DbNamespacedCacheStore;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Post::observe(PostObserver::class);
    }
}
