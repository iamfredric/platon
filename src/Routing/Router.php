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
}
