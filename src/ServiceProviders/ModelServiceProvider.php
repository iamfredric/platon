<?php

namespace Platon\ServiceProviders;

use Platon\Application;
use Platon\Database\Model;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * @param \Platon\Application $app
     *
     * @return void
     */
    public function boot(Application $app)
    {
        $app->bind(Model::class, function ($class) {
            return $class::current();
        });
    }
}