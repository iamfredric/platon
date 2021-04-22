<?php

namespace Platon\Support\Transformers;

class Transformations
{
    protected $attributes;

    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    public function through(string $classname, ...$args)
    {
        $this->attributes = (new $classname($this->attributes, ...$args))
            ->transform();

        return $this;
    }

    public function output()
    {
        return $this->attributes;
    }
}