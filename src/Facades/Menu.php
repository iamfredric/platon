<?php

namespace Platon\Facades;

/**
 * @method static Platon\Menus\MenuRegistrator register($slug, $label)
 * @method static Platon\Menus\MenuRegistrator render($slug, $args = [])
 */
class Menu extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'menu';
    }
}
