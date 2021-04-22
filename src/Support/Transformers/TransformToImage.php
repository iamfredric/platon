<?php

namespace Platon\Support\Transformers;

use Platon\Wordpress\Image;

class TransformToImage
{
    /**
     * @var mixed
     */
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function transform()
    {
        return is_array($this->value) && isset($this->value['sizes']) && isset($this->value['width']) && isset($this->value['height'])
            ? new Image($this->value)
            : $this->value;
    }
}