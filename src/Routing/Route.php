<?php

namespace Platon\Routing;

class Route
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $endpoint;

    /**
     * @var array
     */
    protected $optons;

    /**
     * Route constructor.
     *
     * @param string $name
     * @param array $endpoint
     * @param array $optons
     */
    public function __construct($name, $endpoint = null, $optons = [])
    {
        $this->name = $name;
        $this->endpoint = $endpoint;

        $this->register();
        $this->optons = $optons;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return void
     */
    public function register()
    {
        add_filter($this->hook(), function ($template)
        {
            return $this->extractName($template);
        });
    }

    /**
     * @param string $template
     *
     * @return string
     */
    protected function extractName($template)
    {
        $queriedObject = get_queried_object();

        $queryString = null;

        if ($queriedObject instanceof \WP_Post_Type) {
            $queryString = $queriedObject->name;
        }

        if ($queriedObject instanceof \WP_Term) {
            $queryString = $queriedObject->taxonomy;
        }

        if ($queriedObject instanceof \WP_Post) {
            if ($queriedObject->post_type == 'page') {
                $queryString = $queriedObject->slug;
            } else {
                $queryString = $queriedObject->post_type;
            }
        }

        if ($this->getType() == $queryString) {
            return $this->name;
        }

        $hook = str_replace('_template', '', $this->hook());

        return $this->name == $hook ? $hook : $template;
    }

    /**
     * @return string
     */
    protected function getType()
    {
        $type = explode(':', $this->name);

        if (strpos($this->name, 'single-') > -1) {
            return str_replace('single-', '', $this->name);
        }

        array_shift($type);

        return implode(':', $type);
    }

    /**
     * @return bool
     */
    protected function typeIsDefined()
    {
        return strpos($this->name, ':') > -1;
    }

    /**
     * @return string
     */
    protected function hook()
    {
        [$hook] = explode(':', $this->name);

        if (strpos($this->name, 'single-') > -1) {
            $hook = 'single';
        }

        if ($hook == 'front-page') {
            $hook = 'frontpage';
        }

        return "{$hook}_template";
    }

    /**
     * @return mixed|string
     */
    public function getClassName()
    {
        if (! is_array($this->endpoint)) {
            if (strpos($this->endpoint, '@') > -1) {
                [$classname] = explode('@', $this->endpoint);

                return $classname;
            }

            if (strpos($this->endpoint, '.') > -1) {
                [$classname] = explode('.', $this->endpoint);

                return $classname;
            }

            return $this->endpoint;
        }

        return $this->endpoint[0];
    }

    /**
     * @return string|null
     */
    public function getMethodName()
    {
        if (! is_array($this->endpoint)) {
            if (strpos($this->endpoint, '@') > -1) {
                [$classname, $methodname] = explode('@', $this->endpoint);

                return $methodname;
            }

            if (strpos($this->endpoint, '.') > -1) {
                [$classname, $methodname] = explode('.', $this->endpoint);

                return $methodname;
            }

            return '__invoke';
        }

        return $this->endpoint[1] ?? null;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getCallable()
    {
        return [$this->getClassName(),$this->getMethodName()];
    }

    /**
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function resolve()
    {
        return app()->call($this->getClassName(), $this->getMethodName() ?: '__invoke');
    }

    /**
     * @return bool
     */
    public function isCallable()
    {
        if (is_array($this->endpoint)) {
            return false;
        }

        return is_callable($this->endpoint);
    }

    /**
     * @return mixed
     */
    public function call()
    {
        return call_user_func($this->endpoint);
    }
}
