<?php

namespace Platon\Facades;

use Platon\Posttypes\PostTypeRegistrator;

/**
 * @method static PostTypeRegistrator register($slug)
 * @return \Platon\Posttypes\Posttype
 */
class Posttype extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PostTypeRegistrator::class;
    }
}
