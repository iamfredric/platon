<?php

namespace Platon\ServiceProviders;

use Platon\Application;
use Platon\Support\Translations;
use Platon\Utilities\HookHandler;

class WpmlServiceProvider extends ServiceProvider
{
    public function boot(Application $app)
    {
        if (config('app.scan_for_wpml_string_translations') !== true) {
            return;
        }

        $app->booted(function () use ($app) {
            $app->make(HookHandler::class)
                ->action('init', function () {
                    Translations::strings(
                        apply_filters('platon_translations_folders', [theme_path('resources/views')])
                    )->each(
                        fn ($string) => $this->registerString($string)
                    );

                    $this->updateConfigFile(theme_path('config/app.php'));
                });
        });
    }

    protected function registerString(string $string): void
    {
        do_action(
            'wpml_register_single_string',
            config('app.theme-slug'),
            null,
            $string
        );
    }

    protected function updateConfigFile(string $path): void
    {
        file_put_contents(
            $path,
            preg_replace(
                "/'scan_for_wpml_string_translations' ?=> ?true/",
                "'scan_for_wpml_string_translations' => false",
                file_get_contents($path)
            )
        );
    }
}
