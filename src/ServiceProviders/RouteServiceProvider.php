<?php

namespace Platon\ServiceProviders;

use Platon\Application;
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
    }
}
