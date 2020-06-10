<?php

namespace Platon\ServiceProviders;

use Platon\Application;
use Platon\Media\ImageRegistrator;

class ImageServiceProvider extends ServiceProvider
{
    public function boot(Application $app)
    {
        $app->singleton(ImageRegistrator::class, function () {
            return new ImageRegistrator(config('paths.images'));
        }, true);
    }
}
