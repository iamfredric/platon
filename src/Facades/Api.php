<?php

namespace Platon\Facades;

use Platon\Routing\ApiRouter;

/**
 * @method static \Platon\Routing\ApiRouter get($uri, $endpoint)
 * @method static \Platon\Routing\ApiRouter post($uri, $endpoint)
 * @method static \Platon\Routing\ApiRouter delete($uri, $endpoint)
 */
class Api extends Facade
{
    public static function getFacadeAccessor()
    {
        return ApiRouter::class;
    }
}
