<?php

namespace Platon\Media;

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
     * ImageRegistrator constructor.
     *
     * @param $configFilePath
     */
    public function __construct($configFilePath)
    {
        $this->getRoutes($configFilePath)
             ->registerRoutes();
    }

    /**
     * @param mixed ...$types
     */
    public function support(...$types)
    {
        $this->types = $types;
    }

    /**
     * @param $name
     * @param null $width
     * @param null $height
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
            $image->crop($crop);
        }

        $this->images[] = $image;

        return $image;
    }

    /**
     * @param $configFilePath
     *
     * @return $this
     */
    protected function getRoutes($configFilePath)
    {
        $image = $this;

        include_once $configFilePath;

        return $this;
    }

    /**
     * @return $this
     */
    protected function registerRoutes()
    {
        if (! count($this->images)) {
            return $this;
        }

        add_action('init', function () {
            add_theme_support('post-thumbnails', $this->types);

            foreach ($this->images as $image) {
                $image->register();
            }
        });
    }
}
