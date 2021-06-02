<?php

namespace Platon\Routing;

use Platon\Application;
use Platon\Http\Response;

class Router
{
    /**
     * @var \Platon\Application
     */
    private $app;

    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @var array
     */
    protected $templates = [];

    /**
     * @var array
     */
    protected $uris = [];

    /**
     * @var array
     */
    protected $apiRoutes = [];

    /**
     * Router constructor.
     *
     * @param \Platon\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return void
     */
    public function finalize()
    {
        add_filter('theme_page_templates', function($templates) {
            foreach ($this->templates as $key => $custom) {
                $templates[$key] = $custom->getName();
            }

            return $templates;
        });

        add_filter('template_include', function ($template) {

            if (in_array(get_query_var('pagename'), array_keys($this->uris))) {
                return $this->customRouteResponse($this->uris[get_query_var('pagename')]);
            }

            $originalTemplate = $template;

            if (strpos($template, '.php') > -1) {
                $template = explode('/', str_replace('.php', '', $template));
                $template = end($template);
            }

            if ($template != 'search' && $this->routeIsDefined($key = get_post_meta(get_the_ID(), '_wp_page_template', true))) {
                return $this->routeResponse($this->templates[$key]);
            }

            if ($type = get_post_type()) {
                $typeTemplate = "{$template}-{$type}";

                if ($this->routeIsDefined($typeTemplate)) {
                    return $this->routeResponse($this->routes[$typeTemplate]);
                }
            }

            if ($this->routeIsDefined($template)) {
                return $this->routeResponse($this->routes[$template]);
            }

            return $originalTemplate;
        });

        // Register custom routes
        foreach ($this->uris as $uri) {
            add_action('init', function () use ($uri) {
                add_rewrite_rule($uri->getRegex(), $uri->getQuery(), 'top');
            });

            add_action('query_vars', function ($vars) use ($uri) {
                foreach ($uri->getQueryVars() as $var) {
                    array_push($vars, $var);
                }

                return $vars;
            });
        }
    }

    /**
     * @param string $name
     * @param array $endpoint
     * @param array $options
     *
     * @return void
     */
    public function register($name, $endpoint, $options = [])
    {
        $this->routes[$name] = new Route($name, $endpoint, $options);
    }

    /**
     * @param string $key
     * @param string $name
     * @param array $endpoint
     * @param array $options
     *
     * @return void
     */
    public function template($key, $name, $endpoint, $options = [])
    {
        $this->templates[$key] = new Route($name, $endpoint, $options);
    }

    /**
     * @param $uri
     * @param $endpoint
     */
    public function get($uri, $endpoint)
    {
        $route = new CustomRoute($uri, $endpoint);

        $this->uris[$route->id()] = $route;
    }

    /**
     * @param string $route
     *
     * @return bool
     */
    protected function routeIsDefined($route)
    {
        return isset($this->routes[$route]) || isset($this->templates[$route]);
    }

    /**
     * @param \Platon\Routing\Route $route
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    protected function routeResponse(Route $route)
    {
        $response = $route->isCallable()
            ? $route->call()
            : $this->app->call($route->getClassName(), $route->getMethodName());


        if ($response instanceof Response) {
            echo $response;
            exit;
        }

        echo new Response(
            $response
        );
    }

    protected function customRouteResponse(CustomRoute $route)
    {
        $args = [];
        $dependencies = [];

        foreach ($route->getQueryVars() as $arg) {
            $args[$arg] = get_query_var($arg);
        }

        if ($route->isCallable()) {
            $callable = new \ReflectionFunction($route->getCallable());

            foreach ($callable->getParameters() as $parameter) {
                if ($parameter->getType()) {
                    $dependencies[] = $this->app->make($parameter->getType()->getName());
                } elseif (isset($args[$parameter->getName()])) {
                    $dependencies[] = $args[$parameter->getName()];
                } else {
                    $dependencies[] = array_shift($args);
                }
            }

            $response = $callable->invokeArgs($dependencies);

            if ($response instanceof Response) {
                echo $response;
                exit;
            }

            echo new Response(
                $response
            );
            return;
        }

        $class = $this->app->call($route->getClassName());

        $method = new \ReflectionMethod($class, $route->getMethodName());

        foreach ($method->getParameters() as $parameter) {
            if ($parameter->getType()) {
                $dependencies[] = $this->app->make($parameter->getType()->getName());
            } elseif (isset($args[$parameter->getName()])) {
                $dependencies[] = $args[$parameter->getName()];
            } else {
                $dependencies[] = array_shift($args);
            }
        }

        $response = $method->invokeArgs(
            $class, $dependencies
        );

        if ($response instanceof Response) {
            echo $response;
            exit;
        }

        echo new Response(
            $response
        );
    }
}
