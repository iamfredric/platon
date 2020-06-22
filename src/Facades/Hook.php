<?php

namespace Platon\Facades;

use Platon\Utilities\HookHandler;

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
