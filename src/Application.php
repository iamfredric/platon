<?php

namespace Platon;

use Platon\Facades\Facade;
use Platon\ServiceProviders\AcfAdminServiceProvider;
use Platon\ServiceProviders\HookServiceProvider;
use Platon\ServiceProviders\ImageServiceProvider;
use Platon\ServiceProviders\LoginServiceProvider;
use Platon\ServiceProviders\MenuServiceProvider;
use Platon\ServiceProviders\ModelServiceProvider;
use Platon\ServiceProviders\OptimisationsServiceProvider;
use Platon\ServiceProviders\PostTypeServiceProvider;
use Platon\ServiceProviders\RouteServiceProvider;
use Platon\ServiceProviders\ServiceProvider;
use Platon\ServiceProviders\SupportServiceProvider;
use Platon\ServiceProviders\ViewServiceProvider;
use Platon\ServiceProviders\WpdbServiceProvider;
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
        PostTypeServiceProvider::class,
        AcfAdminServiceProvider::class,
        HookServiceProvider::class,
        SupportServiceProvider::class,
        LoginServiceProvider::class,
        WpdbServiceProvider::class
    ];

    /**
     * @param string $path
     *
     * @return void
     */
    public function autoload($path)
    {
        $this->autoloadPaths[] = $path;
    }

    /**
     * @param string|callable $abstract
     * @param mixed $method
     *
     * @return void
     */
    public function booted($abstract, $method = null)
    {
        $this->actionsOnBoot[] = compact('abstract', 'method');
    }

    /**
     * Sets the application instance
     *
     * @param \Platon\Application $instance
     *
     * @return void
     */
    public static function setInstance($instance)
    {
        static::$instance = $instance;
    }

    /**
     * @param string $abstract
     * @param mixed $concrete
     * @param bool $resolve
     *
     * @return void
     */
    public function singleton($abstract, $concrete = null, $resolve = false)
    {
        $this->bind($abstract, $concrete, true, $resolve);
    }

    /**
     * @param string $abstract
     * @param mixed $concrete
     * @param bool $shared
     * @param bool $resolve
     *
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false, $resolve = false)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = compact('concrete', 'shared', 'resolve');
    }

    /**
     * @param string $classname
     * @param null|string $methodname
     *
     * @return mixed
     *
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
            throw new \Exception("{$methodname} is not a callable method");
        }

        $dependencies = [];

        foreach ($method->getParameters() as $parameter) {
            $dependencies[] = $this->make($parameter->getType()->getName());
        }

        return $method->invokeArgs(
            $class, $dependencies
        );
    }

    /**
     * @param string $abstract
     *
     * @return mixed
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
                    return $this->bindings[$parent]['concrete']($abstract);
                }
            }

            if ($reflection->isInstantiable()) {
                $dependencies = [];
                if ($constructor = $reflection->getConstructor()) {
                    throw new \Exception('Unfinished');
                }


                return $reflection->newInstanceArgs($dependencies);
            }

            throw new \Exception('Unfinished');
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
        if (! static::$instance instanceof Application) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * Register serviceprovider
     *
     * @param string $provider
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
     * @throws \ReflectionException
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
     * @return $this
     * @throws \ReflectionException
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
     * @return $this
     * @throws \ReflectionException
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
     *
     * @return void
     */
    protected function bootProvider(ServiceProvider $provider)
    {
        if (method_exists($provider, 'boot')) {
            $provider->boot($this);
        }
    }
}
