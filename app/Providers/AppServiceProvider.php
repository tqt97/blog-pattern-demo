<?php

namespace App\Providers;

use App\Cache\Adapters\DbNamespacedCacheStore;
use App\Cache\Adapters\RedisNamespacedCacheStore;
use App\Cache\Contracts\NamespacedCacheStoreInterface;
use App\Filters\Contracts\QueryFilter;
use App\Filters\Domains\PostFilters;
use App\Models\Post;
use App\Observers\PostObserver;
use Illuminate\Database\Eloquent\Model;
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

        // $this->app->bind(
        //     QueryFilter::class . ':' . Post::class,
        //     PostFilters::class
        // );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Post::observe(PostObserver::class);
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());
    }
}
