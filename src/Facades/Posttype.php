<?php

namespace Platon\Facades;

use Platon\Posttypes\PostTypeRegistrator;

/**
 * @method static PostTypeRegistrator register($slug)
 * @method static PostTypeRegistrator taxonomy($id)
 */
class Posttype extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PostTypeRegistrator::class;
    }
}
