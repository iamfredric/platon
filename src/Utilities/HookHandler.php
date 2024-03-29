<?php

namespace Platon\Utilities;

class HookHandler
{
    /**
     * @param string $name
     * @param mixed $callable
     * @param int $priority
     * @param int $acceptedArgs
     *
     * @return void
     */
    public function action($name, $callable, $priority = 10, $acceptedArgs = 1)
    {
        add_action($name, function (...$args) use ($callable) {
            return $this->resolve($callable, $args);
        }, $priority, $acceptedArgs);
    }

    /**
     * @param string $name
     * @param mixed $callable
     * @param int $priority
     * @param int $acceptedArgs
     *
     * @return void
     */
    public function filter($name, $callable, $priority = 10, $acceptedArgs = 1)
    {
        add_filter($name, function (...$args) use ($callable) {
            return $this->resolve($callable, $args);
        }, $priority, $acceptedArgs);
    }

    /**
     * @param callable|string|array $callable
     * @param mixed ...$args
     *
     * @return mixed
     * @throws \ReflectionException
     */
    protected function resolve($callable, ...$args)
    {
        if (! is_array($callable) && is_callable($callable)) {
            return call_user_func_array($callable, ...$args);
        }

        [$callable, $method] = is_array($callable) ? $callable : [$callable, 'handle'];

        return call_user_func_array([app()->make($callable), $method], ...$args);
    }
}
