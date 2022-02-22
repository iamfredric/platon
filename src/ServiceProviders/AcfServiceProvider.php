<?php

namespace Platon\ServiceProviders;

use Platon\Application;
use Platon\Wordpress\AdvancedCustomFields\AcfConfigurator;

class AcfServiceProvider extends ServiceProvider
{
    /**
     * @inheritDoc
     */
    public function boot(Application $app)
    {
        $app->singleton(AcfConfigurator::class, function () {
            return new AcfConfigurator();
        });

        if ($path = config('paths.acf')) {
            $app->autoload($path);

            add_action(
                'acf/init',
                fn () => $app->make(AcfConfigurator::class)->register()
            );
        }
    }
}