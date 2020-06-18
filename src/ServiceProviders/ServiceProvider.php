<?php

namespace Platon\ServiceProviders;

use Platon\Application;

abstract class ServiceProvider
{
    /**
     * @param \Platon\Application $app
     *
     * @return void
     */
    abstract public function boot(Application $app);
}