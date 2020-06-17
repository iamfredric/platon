<?php

namespace Platon\Routing;

use Iamfredric\Instantiator\Instantiator;

class Route
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var null
     */
    protected $endpoint;

    public function __construct($name, $endpoint = null)
    {
        $this->name = $name;
        $this->endpoint = $endpoint;

        $this->register();
    }

    public function getName()
    {
        return $this->name;
    }

    public function register()
    {
        add_filter($this->hook(), function ($template)
        {
            return $this->typeIsDefined()
                ? $this->extractName($template)
                : $this->name;
        });
    }

    protected function extractName($template)
    {
        $queriedObject = get_queried_object();

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

        return $template;
    }

    protected function getType()
    {
        $type = explode(':', $this->name);

        array_shift($type);

        return implode(':', $type);
    }

    protected function typeIsDefined()
    {
        return strpos($this->name, ':') > -1;
    }

    protected function hook()
    {
        [$hook] = explode(':', $this->name);

        if ($hook == 'front-page') {
            $hook = 'frontpage';
        }

        return "{$hook}_template";
    }

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
        }

        return $this->endpoint[0];
    }

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
            // Todo: throw exception
        }

        return $this->endpoint[1] ?? null;
    }

    public function getCallable()
    {
        return [$this->getClassName(),$this->getMethodName()];
    }

    public function resolve()
    {
        $class = new Instantiator($this->getClassName());

        if ($method =$this->getMethodName()) {
            return $class->callMethod($this->getMethodName());
        }

        return $class->call()();
    }
}
