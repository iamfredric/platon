<?php

namespace Platon\ServiceProviders;

use Platon\Application;
use Platon\Routing\ApiRouter;
use Platon\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * @param \Platon\Application $app
     *
     * @return void
     */
    public function boot(Application $app)
    {
        $app->singleton(Router::class, function () use ($app) {
            return new Router($app);
        }, true);

        $app->autoload(config('paths.routes'));

        $app->booted(Router::class, 'finalize');

        if (config('paths.api_routes')) {
            $app->singleton(ApiRouter::class, function () use ($app) {
                return new ApiRouter($app, config('app.api.namespace') ?: 'platon');
            });

            $app->autoload(config('paths.api_routes'));

            $app->booted(ApiRouter::class, 'finalize');
        }
    }
}
