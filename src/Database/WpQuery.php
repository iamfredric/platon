<?php

namespace Platon\Database;

class WpQuery
{
    protected static $instance = \WP_Query::class;

    public static function make($arguments)
    {
        if (is_callable(static::$instance)) {
            return call_user_func(self::$instance, $arguments);
        }

        return new static::$instance($arguments);
    }

    public static function setInstance($instance)
    {
        static::$instance = $instance;
    }
}
