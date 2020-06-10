<?php

namespace Platon\Media;

class ImageSize
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    /**
     * @var bool
     */
    public $crop = true;

    /**
     * ImageRegistrar constructor.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Sets the width
     *
     * @param $width
     *
     * @return $this
     */
    public function width($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Sets the height
     *
     * @param $height
     *
     * @return $this
     */
    public function height($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Enables crop
     *
     * @return $this
     */
    public function crop()
    {
        $this->crop = true;

        return $this;
    }

    /**
     * Enables scaling
     *
     * @return $this
     */
    public function scale()
    {
        $this->crop = false;

        return $this;
    }

    /**
     * Registers the image
     */
    public function register()
    {
        add_image_size($this->name, $this->width, $this->height, $this->crop);
        add_image_size($this->name . '@2x', $this->width*2, $this->height*2, $this->crop);
    }
}
