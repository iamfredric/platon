<?php

namespace Platon\ServiceProviders;

use Platon\Application;
use Platon\Facades\Hook;

class AcfAdminServiceProvider extends ServiceProvider
{
    /**
     * @param \Platon\Application $app
     *
     * @return void
     */
    public function boot(Application $app)
    {
        $app->booted(function () use ($app) {
            if (! function_exists('acf_add_options_page')) {
                return;
            }

            // Hiding acf in admin if not in debug mode
            Hook::filter('acf/settings/show_admin', function () {
                return WP_DEBUG;
            });

            // Loading options pages
            if (! $options = config('acf.options')) {
                return;
            }

            // If multiple
            if (is_array($options) && isset($options['menu_slug'])) {
                acf_add_options_page($options);

                if (isset($options['share']) && $options['share'] === true) {
                    $app->make('view')->share($options['post_id'], collect(get_fields($options['post_id'])));
                }

                return;
            }

            // If single one
            foreach ($options as $option) {
                acf_add_options_page($option);

                if (isset($option['share']) && $option['share'] === true) {
                    $app->make('view')->share($option['post_id'], collect(get_fields($option['post_id'])));
                }
            }
        });
    }
}
