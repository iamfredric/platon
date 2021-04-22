<?php

namespace Platon\Support\Transformers;

class AutoCaster
{
    protected $attributes;

    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    public function transform()
    {
        foreach ($this->attributes as $key => $value) {
            $this->attributes[$key] = (new Transformations($value))
                ->through(TransformToImage::class)
                ->through(TransformToLink::class)
                ->output();
        }

        return $this->attributes;
    }
}