<?php

namespace Platon\Support\Transformers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MapKeysToCamel
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * MapKeysToCamel constructor.
     *
     * @param $attributes
     */
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function transform()
    {
        $attributes = [];

        foreach ($this->attributes as $key => $attribute) {
            $attributes[Str::camel($key)] = $attribute;
        }

        return $attributes;
    }
}