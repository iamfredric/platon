<?php

namespace Platon\Facades;

use Platon\Routing\ApiRouter;

/**
 * @method static \Platon\Routing\ApiRouter get($uri, $endpoint, $namespace = null)
 * @method static \Platon\Routing\ApiRouter post($uri, $endpoint, $namespace = null)
 */
class Api extends Facade
{
    public static function getFacadeAccessor()
    {
        return ApiRouter::class;
    }
}