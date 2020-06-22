<?php

namespace Platon\ServiceProviders;

use Illuminate\Support\Str;
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

        $app->booted(function () {
            add_filter('sanitize_file_name', function ($filename) {
                [$filename, $extension] = explode('.', $filename);

                return (string) Str::of($filename)->slug()->append(".{$extension}");
            });
        });
    }
}
