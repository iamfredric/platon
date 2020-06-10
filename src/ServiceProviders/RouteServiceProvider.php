<?php

namespace Platon\ServiceProviders;

use Platon\Application;
use Platon\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(Application $app)
    {
        $app->singleton(Router::class, function () use ($app) {
            return new Router($app, config('app.routes_file'));
        }, true);
    }
}
