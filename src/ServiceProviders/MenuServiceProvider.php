<?php

namespace Platon\ServiceProviders;

use Platon\Application;
use Platon\Menus\MenuRegistrator;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * @param \Platon\Application $app
     *
     * @return void
     */
    public function boot(Application $app)
    {
        $app->singleton('menu', function () {
            return new MenuRegistrator(config('app.slug'));
        }, true);

        $app->autoload(config('paths.menus'));
    }
}
