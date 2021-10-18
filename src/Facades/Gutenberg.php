<?php

namespace Platon\Facades;

/**
 * @method static \Platon\Gutenberg\Gutenberg block(string $block): \Platon\Gutenberg\Gutenberg
 */
class Gutenberg extends Facade
{
    public static function getFacadeAccessor()
    {
        return \Platon\Gutenberg\Gutenberg::class;
    }
}
