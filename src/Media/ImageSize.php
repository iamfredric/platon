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
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;

        $this->extractSizeFromName();
    }

    /**
     * Sets the width
     *
     * @param int $width
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
     * @param int $height
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
     *
     * @return void
     */
    public function register()
    {
        add_image_size($this->name, $this->width, $this->height, $this->crop);
    }

    /**
     * @return void
     */
    protected function extractSizeFromName()
    {
        if (!! $this->width || !! $this->height) {
            return;
        }

        if (preg_match("/^([0-9]+)x([0-9]+)$/", $this->name)) {
            [$width, $height] = explode('x', $this->name);

            $this->width($width)
                 ->height($height);

        }
    }
}
