<?php

namespace Platon\Support\Transformers;

use Illuminate\Support\Str;

class AttributesWhenNull
{

    protected $attributes;
    protected $instance;

    public function __construct($attributes, $instance)
    {
        $this->attributes = $attributes;
        $this->instance = $instance;
    }

    /**
     * @return mixed
     */
    public function transform()
    {
        foreach ($this->attributes as $key => $item) {
            if (! $item) {
                $methodName = $this->getMethodNameByKey($key);

                if (method_exists($this->instance, $methodName)) {
                    $this->attributes[$key] = $this->instance->{$methodName}();
                }
            }
        }

        return $this->attributes;
    }

    /**
     * @param $key
     *
     * @return string
     */
    protected function getMethodNameByKey($key)
    {
        return (string) Str::of($key)
           ->camel()
           ->ucfirst()
           ->prepend('when')
           ->append('IsNull');
    }
}