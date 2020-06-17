<?php

namespace Platon\Routing;

use Platon\Application;

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

            if ($this->routeIsDefined($template)) {
                return $this->routeResponse($this->routes[$template]);
            }

            return $originalTemplate;
        });
    }

    /**
     * @param $name
     * @param $endpoint
     * @param array $options
     */
    public function register($name, $endpoint, $options = [])
    {
        $this->routes[$name] = new Route($name, $endpoint, $options);
    }

    /**
     * @param $key
     * @param $name
     * @param $endpoint
     * @param array $options
     */
    public function template($key, $name, $endpoint, $options = [])
    {
        $this->templates[$key] = new Route($name, $endpoint, $options);
    }

    protected function routeIsDefined($route)
    {
        return isset($this->routes[$route]) || isset($this->templates[$route]);
    }

    protected function routeResponse(Route $route)
    {
        echo $this->app->call($route->getClassName(), $route->getMethodName());
    }
}
