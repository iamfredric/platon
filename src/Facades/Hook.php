<?php

namespace Platon\Facades;

use Platon\Utilities\HookHandler;

/**
 * @method static HookHandler action($name, $callable, $priority = 10, $acceptedArgs = 1)
 * @method static HookHandler filter($name, $callable, $priority = 10, $acceptedArgs = 1)
 */
class Hook extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return HookHandler::class;
    }
}
