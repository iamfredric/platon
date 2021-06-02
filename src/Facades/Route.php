<?php

namespace Platon\Facades;

use Platon\Routing\Router;

/**
 * @method static \Platon\Routing\Router register($name, $endpoint, $options = [])
 * @method static \Platon\Routing\Router template($key, $name, $endpoint, $options = [])
 * @method static \Platon\Routing\Router get($path, $endpoint)
 */
class Route extends Facade
{
    public static function getFacadeAccessor()
    {
        return Router::class;
    }
}
