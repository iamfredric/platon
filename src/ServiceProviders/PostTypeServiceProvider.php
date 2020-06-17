<?php

namespace Platon\ServiceProviders;

use Platon\Application;
use Platon\Posttypes\PostTypeRegistrator;

class PostTypeServiceProvider extends ServiceProvider
{
    public function boot(Application $app)
    {
        $app->singleton(PostTypeRegistrator::class, function () {
            return new PostTypeRegistrator();
        });

        $app->autoload(config('paths.posttypes'));

        $app->booted(PostTypeRegistrator::class, 'finalize');
    }
}
