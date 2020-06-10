<?php

namespace Platon\Routing;

use Illuminate\Contracts\Support\Jsonable;
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
     * @param $routesFile
     */
    public function __construct(Application $app, $routesFile)
    {
        $this->app = $app;
        $this->registerRoutes($routesFile);
    }

    /**
     * @param $routesFile
     */
    protected function registerRoutes($routesFile)
    {
        include_once $routesFile;

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
        $content = $this->app->call($route->getClassName(), $route->getMethodName());

        try {
            echo new Response($content);
        } catch (\Exception $e) {
            echo '<pre>';
            echo $e;
            exit;
            return;
            foreach ($e->getTrace()->__toString() as $line) {
                dd($line->getMessage());
                echo $line . PHP_EOL;
            }

            exit;
        }

        return;

        if ($content instanceof Illuminate\View\View) {
            echo $content->render();
            return;
        }

        if ($content instanceof Jsonable) {
            header('Content-type: JSON');
            echo $content->toJson();
            return;
        }

        if (is_array($content) or is_object($content)) {
            header('Content-type: JSON');

            echo json_encode($content);
            return;
        }

        echo $content;
    }
}
