<?php

namespace Platon\ServiceProviders;

use Platon\Application;
use Platon\Facades\Hook;

class LoginServiceProvider extends ServiceProvider
{

    public function boot(Application $app)
    {
        $app->booted(function () {
            // Load stylesheet
            if (file_exists(theme_path(config('paths.assets') . '/css/login.css'))) {
                Hook::action('login_enqueue_scripts', function () {
                    wp_dequeue_style('login');

                    wp_register_style(config('app.theme-slug') . '-login-style', mix('css/login.css'));

                    wp_enqueue_style(config('app.theme-slug') . '-login-style');
                });
            }

            // Update the header link url
            Hook::filter('login_headerurl', function () {
                return '/';
            });

            // Update the header logo
            if (file_exists(theme_path(config('paths.assets') . '/img/logo.svg'))) {
                Hook::filter('login_headertext', function () {
                    return '<img src="' . assets('img/logo.svg') . '" alt="' . config('app.title') . '">';
                });
            }
        });
    }
}