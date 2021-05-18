<?php

namespace Platon\ServiceProviders;

use Platon\Application;
use wpdb;

class WpdbServiceProvider extends ServiceProvider
{
    /**
     * @inheritDoc
     */
    public function boot(Application $app)
    {
        $app->singleton(wpdb::class, function () {
            return new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
        });
    }
}