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
            // Todo: throw exception
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
     * @throws \Iamfredric\Instantiator\Exceptions\InstantiationException
     */
    public function resolve()
    {
        $class = new Instantiator($this->getClassName());

        if ($method =$this->getMethodName()) {
            return $class->callMethod($this->getMethodName());
        }

        return $class->call()();
    }
}
