<?php

namespace Platon\ServiceProviders;

use Platon\Application;

class AcfAdminServiceProvider extends ServiceProvider
{
    /**
     * @param \Platon\Application $app
     *
     * @return void
     */
    public function boot(Application $app)
    {
        if (! $config = config('app.admin.acf')) {
            return;
        }

        if (! function_exists('acf_add_options_page')) {
            return;
        }

        $app->booted(function () use ($config, $app) {
            foreach ($config as $key => $item) {
                acf_add_options_page([
                    'page_title' => __($item['title'], config('app.theme-slug')),
                    'menu_slug' => config('app.theme-slug') . '-theme-' . $key,
                    'capability' => $item['capability'] ?? 'edit_posts',
                    'position' => $item['position'] ?? 99.627,
                    'parent_slug' => $item['parent'] ?? null,
                    'post_id' => $key,
                    'autoload' => $item['autoload'] ?? false
                ]);

                if (isset($item['share']) && $item['share'] === true) {
                    $app->make('view')->share($key, collect(get_fields($key)));
                }
            }
        });
    }
}
