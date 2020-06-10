<?php

namespace Platon\ServiceProviders;

use Jenssegers\Blade\Blade;
use Platon\Application;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(Application $app)
    {
        $app->singleton('view', function () {
            return new Blade(
                config('paths.views'),
                config('paths.views_cache')
            );
        });
    }
}
