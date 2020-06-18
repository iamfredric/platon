<?php

namespace Platon\ServiceProviders;

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
            $blade = new Blade(
                config('paths.views'),
                config('paths.views_cache')
            );

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
