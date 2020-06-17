<?php

namespace Platon;

use Illuminate\Support\Str;
use Platon\Facades\Facade;
use Platon\Facades\Image;
use Platon\Facades\Route;
use Platon\Media\ImageRegistrator;
use Platon\ServiceProviders\ImageServiceProvider;
use Platon\ServiceProviders\MenuServiceProvider;
use Platon\ServiceProviders\ModelServiceProvider;
use Platon\ServiceProviders\OptimisationsServiceProvider;
use Platon\ServiceProviders\PostTypeServiceProvider;
use Platon\ServiceProviders\RouteServiceProvider;
use Platon\ServiceProviders\ServiceProvider;
use Platon\ServiceProviders\ViewServiceProvider;
use ReflectionMethod;

class Application
{
    /**
     * @var Application
     */
    protected static $instance;

    /**
     * @var array
     */
    protected $bindings = [];

    /**
     * @var array
     */
    protected $resolved = [];

    /**
     * @var array
     */
    protected $autoloadPaths = [];

    /**
     * @var array
     */
    protected $actionsOnBoot = [];

    /**
     * @var string[]
     */
    protected $serviceProviders = [
        RouteServiceProvider::class,
        ViewServiceProvider::class,
        ImageServiceProvider::class,
        ModelServiceProvider::class,
        MenuServiceProvider::class,
        OptimisationsServiceProvider::class,
        PostTypeServiceProvider::class
    ];

    /**
     * @param $path
     */
    public function autoload($path)
    {
        $this->autoloadPaths[] = $path;
    }

    /**
     * @param $abstract
     * @param null $method
     */
    public function booted($abstract, $method = null)
    {
        $this->actionsOnBoot[] = compact('abstract', 'method');
    }

    /**
     * Sets the application instance
     *
     * @param $instance
     */
    public static function setInstance($instance)
    {
        static::$instance = $instance;
    }

    /**
     * @param $abstract
     * @param null $concrete
     * @param bool $resolve
     */
    public function singleton($abstract, $concrete = null, $resolve = false)
    {
        $this->bind($abstract, $concrete, true, $resolve);
    }

    /**
     * @param $abstract
     * @param null $concrete
     * @param bool $shared
     * @param bool $resolve
     */
    public function bind($abstract, $concrete = null, $shared = false, $resolve = false)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = compact('concrete', 'shared', 'resolve');
    }

    /**
     * @param $classname
     * @param null $methodname
     *
     * @return mixed|object
     * @throws \ReflectionException
     */
    public function call($classname, $methodname = null)
    {
        $class = $this->make($classname);

        if (! $methodname) {
            return $class;
        }

        $method = new ReflectionMethod($class, $methodname);

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

    /**
     * @param $abstract
     *
     * @return mixed|object
     * @throws \ReflectionException
     */
    public function make($abstract)
    {
        if (isset($this->resolved[$abstract])) {
            return $this->resolved[$abstract];
        }

        if (! isset($this->bindings[$abstract])) {
            $reflection = new \ReflectionClass($abstract);

            if ($reflection->getParentClass()) {
                $parent = $reflection->getParentClass()->getName();

                if (isset($this->bindings[$parent])) {
                    return $this->bindings[$parent]['concrete']($reflection->getName());
                }
            }

            if ($reflection->isInstantiable()) {
                $dependencies = [];
                if ($constructor = $reflection->getConstructor()) {
                    die(var_dump($constructor->getParameters()));
                }


                return $reflection->newInstanceArgs($dependencies);
            }

            dd('Todo...');
        }

        $resolved = $this->bindings[$abstract]['concrete']();

        if ($this->bindings[$abstract]['shared']) {
            return $this->resolved[$abstract] = $resolved;
        }

        return $resolved;
    }

    /**
     * Gets the application instance
     *
     * @return \Platon\Application
     */
    public static function getInstance()
    {
        if (! static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Register serviceprovider
     *
     * @param $provider
     *
     * @return mixed
     */
    public function register($provider)
    {
        if (! is_array($provider)) {
            return $this->serviceProviders[] = $provider;
        }

        foreach ($provider as $serviceProvider) {
            $this->register($serviceProvider);
        }
    }

    /**
     * This is where the application starts
     *
     * @return void
     */
    public function start()
    {
        static::setInstance($this);

        Facade::setFacadeApplication($this);

        $this->bootProviders()
            ->resolveBindings()
            ->bootAutoloader()
            ->bootAfterBooted();
    }

    /**
     * Boots the registered service providers
     *
     * @return $this
     */
    protected function bootProviders()
    {
        foreach ($this->serviceProviders as $provider) {
            $this->bootProvider(new $provider());
        }

        return $this;
    }

    /**
     * Resolves bindings that should autoresolve
     *
     * @return $this
     */
    protected function resolveBindings()
    {
        foreach ($this->bindings as $abstract => $binding) {
            if ($binding['resolve']) {
                $this->make($abstract);
            }
        }

        return $this;
    }

    /**
     * Resolves action hooks after booted
     *
     * @return $this
     */
    protected function bootAfterBooted()
    {
        foreach ($this->actionsOnBoot as $action)
        {
            if (is_callable($action['abstract'])) {
                $action['abstract']();
            }

            else {
                $this->call($action['abstract'], $action['method']);
            }

        }

        return $this;
    }

    /**
     * Autoloads defined files
     *
     * @return $this
     */
    protected function bootAutoloader()
    {
        foreach ($this->autoloadPaths as $path) {
            if (file_exists($path)) {
                include_once $path;
            }
        }

        return $this;
    }

    /**
     * Boots serviceprovider
     *
     * @param \Platon\ServiceProviders\ServiceProvider $provider
     */
    protected function bootProvider(ServiceProvider $provider)
    {
        if (method_exists($provider, 'boot')) {
            $provider->boot($this);
        }
    }
}
