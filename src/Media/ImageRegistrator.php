<?php

namespace Platon\Media;

/**
 * @mixin \Platon\Media\ImageSize
 */
class ImageRegistrator
{
    /**
     * @var array
     */
    protected $images = [];

    /**
     * @var array
     */
    protected $types = [];

    /**
     * @param mixed ...$types
     *
     * @return $this
     */
    public function support(...$types)
    {
        $this->types = array_unique([...$this->types, ...$types]);

        return $this;
    }

    /**
     * @param string $name
     * @param int $width
     * @param int $height
     * @param bool $crop
     *
     * @return \Platon\Media\ImageSize
     */
    public function register($name, $width = null, $height = null, $crop = false)
    {
        $image = new ImageSize($name);

        if ($width) {
            $image->width($width);
        }

        if ($height) {
            $image->height($height);
        }

        if ($crop) {
            $image->crop();
        }

        $this->images[] = $image;

        return $image;
    }

    /**
     * @return void
     */
    public function finalize()
    {
        if ($this->types) {
            add_action('init', function () {
                add_theme_support('post-thumbnails', $this->types);
            });
        }

        if (count($this->images)) {
            add_action('init', function () {
                foreach ($this->images as $image) {
                    $image->register();
                }
            });
        }
    }
}
