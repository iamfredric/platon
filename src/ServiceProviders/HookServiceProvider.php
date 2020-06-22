<?php

namespace Platon\ServiceProviders;

use Platon\Application;
use Platon\Utilities\HookHandler;

class HookServiceProvider extends ServiceProvider
{
    /**
     * @param \Platon\Application $app
     *
     * @return void
     */
    public function boot(Application $app)
    {
        $app->singleton(HookHandler::class, function () {
            return new HookHandler();
        });

        $app->autoload(config('paths.hooks'));
    }
}
