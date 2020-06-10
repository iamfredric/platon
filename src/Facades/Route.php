<?php

namespace Platon\Facades;

use Platon\Routing\Router;

class Route extends Facade
{
    public static function getFacadeAccessor()
    {
        return Router::class;
    }
}
