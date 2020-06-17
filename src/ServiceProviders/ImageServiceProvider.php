<?php

namespace Platon\ServiceProviders;

use Platon\Application;
use Platon\Media\ImageRegistrator;

class ImageServiceProvider extends ServiceProvider
{
    public function boot(Application $app)
    {
        $app->singleton(ImageRegistrator::class, function () use ($app) {
            return new ImageRegistrator();
        }, true);

        $app->autoload(config('paths.images'));

        $app->booted(ImageRegistrator::class, 'finalize');
    }
}
