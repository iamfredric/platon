<?php

namespace Platon\Database;

trait Macroable
{
    protected static $macros = [];

    /**
     * @param string $name
     * @param callable $callback
     *
     * @return void
     */
    public static function macro($name, $callback)
    {
        static::$macros[$name] = $callback;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasMacro($name)
    {
        return isset(static::$macros[$name]);
    }

    /**
     * @param $name
     * @param ...$args
     *
     * @return void
     */
    public function resolveMacro($name, ...$args)
    {
        static::$macros[$name](...$args);
    }
}
