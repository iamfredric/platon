<?php

namespace Platon\Support\Transformers;

use stdClass;

class Casts
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var null
     */
    protected $cast;

    public function __construct($value, $cast = null)
    {
        $this->value = $value;
        $this->cast = $cast;
    }

    public function transform()
    {
        if ($this->cast === 'stdClass' || $this->cast === 'object') {
            return (object) $this->value;
        }

        if ($this->cast === 'array') {
            return (array) $this->value;
        }

        return $this->cast
            ? new $this->cast($this->value)
            : $this->value;
    }
}