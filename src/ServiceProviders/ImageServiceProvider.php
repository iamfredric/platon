<?php

namespace Platon\ServiceProviders;

use Platon\Application;
use Platon\Media\ImageRegistrator;

class ImageServiceProvider extends ServiceProvider
{
    /**
     * @param \Platon\Application $app
     *
     * @return void
     */
    public function boot(Application $app)
    {
        $app->singleton(ImageRegistrator::class, function () {
            return new ImageRegistrator();
        }, true);

        $app->autoload(config('paths.images'));

        $app->booted(ImageRegistrator::class, 'finalize');
    }
}
