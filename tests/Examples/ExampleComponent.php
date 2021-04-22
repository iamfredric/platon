<?php

namespace PlatonTest\Examples;

use Platon\Media\Link;

class ExampleComponent extends \Platon\Components\Component
{
    protected $casts = [
        'casted' => Link::class,
        'very.*.nested' => Link::class
    ];

    public function whenNullableToBeTransformedIsNull()
    {
        return 'i have been nullified';
    }

    public function getPrefixedAttribute($value)
    {
        return "very-much-{$value}";
    }
}