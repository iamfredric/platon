<?php

namespace Platon\Support\Transformers;

use Illuminate\Support\Str;

class AttributeGetters
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var object
     */
    protected $instance;

    public function __construct($attributes, $instance)
    {
        $this->attributes = $attributes;
        $this->instance = $instance;
    }

    public function transform()
    {
        foreach ($this->attributes as $key => $item) {
            $methodName = $this->translateKeyToMethodName($key);

            if (method_exists($this->instance, $methodName)) {
                $this->attributes[$key] = $this->instance->{$methodName}($item);
            }
        }

        return $this->attributes;
    }

    /**
     * @param $key
     *
     * @return string
     */
    protected function translateKeyToMethodName($key)
    {
        return (string) Str::of($key)
           ->camel()
           ->ucfirst()
           ->prepend('get')
           ->append('Attribute');
    }
}