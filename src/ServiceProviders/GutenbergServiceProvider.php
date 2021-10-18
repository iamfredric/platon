<?php

namespace Platon\ServiceProviders;

use Platon\Application;
use Platon\Gutenberg\Gutenberg;

class GutenbergServiceProvider extends ServiceProvider
{
    public function boot(Application $app)
    {
        if (! config('paths.gutenberg')) {
            return;
        }

        $app->singleton(Gutenberg::class, function () use ($app) {
            return new Gutenberg($app);
        }, true);

        $app->autoload(config('paths.gutenberg'));

        $app->booted(Gutenberg::class, 'finalize');
    }
}