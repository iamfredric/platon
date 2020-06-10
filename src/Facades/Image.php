<?php

namespace Platon\Facades;

use Platon\Media\ImageRegistrator;

/**
 * @method static ImageRegistrator support(...$types)
 * @method static ImageRegistrator register($name, $width = null, $height = null, $crop = false)
 */
class Image extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ImageRegistrator::class;
    }
}
