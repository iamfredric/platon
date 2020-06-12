<?php

namespace Platon;

use Platon\Facades\Route;
use Platon\ServiceProviders\ImageServiceProvider;
use Platon\ServiceProviders\MenuServiceProvider;
use Platon\ServiceProviders\ModelServiceProvider;
use Platon\ServiceProviders\RouteServiceProvider;
use Platon\ServiceProviders\ServiceProvider;
use Platon\ServiceProviders\ViewServiceProvider;

class Application
{
    protected static $instance;

    protected $bindings = [];

    protected $aliases = [
        Route::class
    ];

    protected $serviceProviders = [
        RouteServiceProvider::class,
        ViewServiceProvider::class,
        ImageServiceProvider::class,
        ModelServiceProvider::class,
        MenuServiceProvider::class
    ];

    public function start()
    {
        foreach ($this->serviceProviders as $provider) {
            $this->bootProvider(new $provider());
        }

        foreach ($this->aliases as $alias) {
            $alias::setFacadeApplication($this);
        }

        foreach ($this->bindings as $abstract => $binding) {
            if ($binding['resolve']) {
                $this->make($abstract);
            }
        }

        static::$instance = $this;
    }

    public static function setInstance($instance)
    {
        static::$instance = $instance;
    }

    /**
     * @return \Platon\Application
     */
    public static function getInstance()
    {
        if (! static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function register($provider)
    {
        if (! is_array($provider)) {
            return $this->serviceProviders[] = $provider;
        }

        foreach ($provider as $serviceProvider) {
            $this->register($serviceProvider);
        }
    }

    protected function bootProvider(ServiceProvider $provider)
    {
        if (method_exists($provider, 'boot')) {
            $provider->boot($this);
        }
    }

    public function singleton($abstract, $concrete = null, $resolve = false)
    {
        $this->bind($abstract, $concrete, true, $resolve);
    }

    public function bind($abstract, $concrete = null, $shared = false, $resolve = false)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = compact('concrete', 'shared', 'resolve');
    }

    public function call($classname, $methodname = null)
    {
        $class = $this->make($classname);

        if (! $methodname) {
            return $class;
        }

        $method = new \ReflectionMethod($class, $methodname);

        if (! $method->isPublic()) {
            die('Not a public callable method');
        }

        $dependencies = [];

        foreach ($method->getParameters() as $parameter) {
            $dependencies[] = $this->make($parameter->getClass()->getName());
        }

        return $method->invokeArgs(
            $class, $dependencies
        );
    }

    public function make($abstract)
    {
        if (! isset($this->bindings[$abstract])) {
            $reflection = new \ReflectionClass($abstract);

            if ($reflection->getParentClass()) {
                $parent = $reflection->getParentClass()->getName();

                if (isset($this->bindings[$parent])) {
                    return $this->bindings[$parent]['concrete']($reflection->getName());
                    return $this->make($parent);
                }
            }

            if ($reflection->isInstantiable()) {
                $dependencies = [];
                if ($constructor = $reflection->getConstructor()) {
                    die(var_dump($constructor->getParameters()));
                }


                return $reflection->newInstanceArgs($dependencies);
            }

            die('Done');
            // $reflection->getParentClass();
            $reflection->isInstantiable();

            return $constructor = $reflection->getConstructor();

            return $reflection;
            die(var_dump('Cant resolve this'));
            // Todo: Throw exception
        }

        if (isset($this->bindings[$abstract]['resolved'])) {
            return $this->bindings[$abstract]['resolved'];
        }

        $resolved = $this->bindings[$abstract]['concrete']();

        if ($this->bindings[$abstract]['shared']) {
            $this->bindings[$abstract]['resolved'] = $resolved;
        }

        return $resolved;
    }
}
