<?php

namespace Platon\Routing;

use Platon\Application;
use Platon\Exceptions\RestExceptionHandler;
use Platon\Http\Request;
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

    public function delete($uri, $endpoint)
    {
        $this->routes[] = new ApiRoute('delete', $uri, $endpoint);
    }

    public function finalize()
    {
        add_action('rest_api_init', function () {
            foreach ($this->routes as $route) {
                register_rest_route($this->namespace, $route->uri(), [
                    'methods' => $route->method(),
                    'permission_callback' => '__return_true',
                    'callback' => function ($request) use ($route) {
                        try {
                            return $this->routeResponse($route, $request);
                        } catch (\Exception $e) {
                            return RestExceptionHandler::response($e);
                        }
                    }
                ]);
            }
        });
    }

    /**
     * @throws \ReflectionException
     */
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
            $type = (string) $parameter->getType();
            if ($type === Request::class) {
                $dependencies[] = new Request($request);
            } elseif ($type === 'WP_REST_Request') {
                $dependencies[] = $request;
            } elseif (in_array($parameter->getName(), $params)) {
                $dependencies[] = $request->get_param($parameter->getName());
            } elseif ($parameter->allowsNull()) {
                $dependencies[] = null;
            } elseif ($type) {
                $dependencies[] = $this->app->make($parameter->getClass()->name);
            } else {
                $dependencies[] = null;
            }
        }

        $response = $method->invokeArgs(
            $class, $dependencies
        );

        if ($response instanceof Response) {
            return $response->toJsonResponse();
        }

        return $response;
    }
}
