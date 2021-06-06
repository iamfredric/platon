<?php

namespace Platon\Routing;

use Platon\Application;
use Platon\Http\Response;

class ApiRouter
{
    /**
     * @var \Platon\Application
     */
    protected Application $app;

    /**
     * @var string
     */
    protected $namespace;

    protected $routes = [];

    public function __construct(Application $app, string $namespace)
    {
        $this->app = $app;
        $this->namespace = $namespace;
    }

    public function get($uri, $endpoint)
    {
        $this->routes[] = new ApiRoute('get', $uri, $endpoint);
    }

    public function post($uri, $endpoint)
    {
        $this->routes[] = new ApiRoute('post', $uri, $endpoint);
    }

    public function finalize()
    {
        add_action('rest_api_init', function () {
            foreach ($this->routes as $route) {
                register_rest_route($this->namespace, $route->uri(), [
                    'methods' => $route->method(),
                    'permission_callback' => '__return_true',
                    'callback' => function ($request) use ($route) {
                        return $this->routeResponse($route, $request);
                    }
                ]);
            }
        });
    }

    protected function routeResponse(ApiRoute $route, $request)
    {
        if ($route->isCallable()) {
            return call_user_func($route->endpoint());
        }

        $class = $this->app->call($route->getClassName());

        $method = new \ReflectionMethod($class, $route->getMethodName());

        $params = $route->getUriParams();

        $dependencies = [];

        foreach ($method->getParameters() as $parameter) {
            if ($parameter->getType() === 'WP_REST_Request') {
                $dependencies[] = $request;
            } elseif (in_array($parameter->getName(), $params)) {
                $dependencies[] = $request->get_param($parameter->getName());
            } elseif ($parameter->allowsNull()) {
                $dependencies[] = null;
            } elseif ($parameter->getType()) {
                $dependencies[] = $this->app->make($parameter->getType()->getName());
            } else {
                $dependencies[] = null;
            }
        }

        return $method->invokeArgs(
            $class, $dependencies
        );
    }
}
