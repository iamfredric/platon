<?php

namespace Platon\ServiceProviders;

use Illuminate\Container\Container;
use Illuminate\Contracts\View\Factory;
use Jenssegers\Blade\Blade;
use Platon\Application;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * @param \Platon\Application $app
     *
     * @return void
     */
    public function boot(Application $app)
    {
        $app->singleton('view', function () {
            $application = Container::getInstance();

            $application->bind(
                \Illuminate\Contracts\Foundation\Application::class,
                \Platon\Blade\BladeApplication::class
            );

            $blade = new Blade(
                config('paths.views'),
                config('paths.views_cache')
            );

            $application->bind('view', function () use ($blade) {
                return $blade;
            });

            $application->bind(Factory::class, function () use ($blade) {
                return $blade;
            });

            $blade->directive('wp', function ($name) {
                ob_start();
                $arg = trim($name, '\'');
                $output = "<?php call_user_func('wp_{$arg}'); ?>";
                ob_end_clean();

                return $output;
            });

            return $blade;
        });
    }
}
