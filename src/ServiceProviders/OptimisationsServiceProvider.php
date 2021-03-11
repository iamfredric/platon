<?php

namespace Platon\ServiceProviders;

use Platon\Application;

class OptimisationsServiceProvider extends ServiceProvider
{
    public function boot(Application $app)
    {
        if (! config('app.optimize') || is_admin()) {
            return;
        }

        // Move scripts to footer
        add_action('after_setup_theme', function () {
            remove_action('wp_head', 'wp_print_scripts');
            remove_action('wp_head', 'wp_print_head_scripts', 9);
            remove_action('wp_head', 'wp_enqueue_scripts', 1);
            add_action('wp_footer', 'wp_print_scripts', 5);
            add_action('wp_footer', 'wp_enqueue_scripts', 5);
            add_action('wp_footer', 'wp_print_head_scripts', 5);
        });

        // Move jquery to the footer
        add_action('wp_enqueue_scripts', function () {
            wp_scripts()->add_data('jquery', 'group', 1);
            wp_scripts()->add_data('jquery-core', 'group', 1);
            wp_scripts()->add_data('jquery-migrate', 'group', 1);
        });
    }
}
