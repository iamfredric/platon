<?php

namespace Platon\Facades;

/**
 * @method static \Jenssegers\Blade\Blade render(string $view, array $data = [], array $mergeData = []): string
 * @method static \Jenssegers\Blade\Blade make($view, $data = [], $mergeData = []): View
 * @method static \Jenssegers\Blade\Blade compiler(): BladeCompiler
 * @method static \Jenssegers\Blade\Blade directive(string $name, callable $handler)
 * @method static \Jenssegers\Blade\Blade if($name, callable $callback)
 * @method static \Jenssegers\Blade\Blade exists($view): bool
 * @method static \Jenssegers\Blade\Blade file($path, $data = [], $mergeData = []): View
 * @method static \Jenssegers\Blade\Blade share($key, $value = null)
 * @method static \Jenssegers\Blade\Blade composer($views, $callback): array
 * @method static \Jenssegers\Blade\Blade creator($views, $callback): array
 * @method static \Jenssegers\Blade\Blade addNamespace($namespace, $hints): self
 * @method static \Jenssegers\Blade\Blade replaceNamespace($namespace, $hints): self
 * @method static \Jenssegers\Blade\Blade __call(string $method, array $params)
 */
class View extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'view';
    }
}
