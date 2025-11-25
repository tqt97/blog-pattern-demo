<?php

namespace App\Providers;

use App\Cache\Adapters\DbNamespacedCacheStore;
use App\Cache\Adapters\RedisNamespacedCacheStore;
use App\Cache\Contracts\NamespacedCacheStoreInterface;
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

            // Tùy anh: có thể check theo store name như redis, memcached, dynamodb
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
        //
    }
}
