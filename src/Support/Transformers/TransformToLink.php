<?php

namespace Platon\Support\Transformers;

use Platon\Media\Link;

class TransformToLink
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * TransformToLink constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function transform()
    {
        return is_array($this->value) && isset($this->value['url']) && isset($this->value['title']) && isset($this->value['target'])
            ? new Link($this->value)
            : $this->value;
    }
}