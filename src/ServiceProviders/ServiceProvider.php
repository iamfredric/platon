<?php

namespace Platon\ServiceProviders;

use Platon\Application;

abstract class ServiceProvider
{
    abstract public function boot(Application $app);
}