<?php

namespace Platon\Media;

class Link
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * Link constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->has('url');
    }

    /**
     * @param string $class
     *
     * @return string
     */
    public function render($class = '')
    {
        return '<a href="' . $this->get('url') . '" class="' . $class . '" target="' . $this->get('target') . '">' . $this->get('title') . '</a>';
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->attributes[$key] : $default;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->attributes[$key]);
    }
}
