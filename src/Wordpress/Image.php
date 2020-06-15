<?php

namespace Platon\Wordpress;

class Image
{
    /**
     * @var boolean
     */
    private $hasimage = false;

    /**
     * @var int
     */
    private $thumbnailId;

    /**
     * @var string
     */
    private $thumbnailTitle;

    /**
     * @var string
     */
    private $thumbnailUrl;

    /**
     * @var string
     */
    private $thumbnailAlt;

    /**
     * @var string
     */
    private $thumbnailDescription;

    /**
     * @var string
     */
    private $thumbnailSizes;

    /**
     * @var string
     */
    private $thumbnailCaption;

    private $thumbnailDimensions = [];

    /**
     * @param array $thumbnail
     */
    public function __construct($thumbnail = null)
    {
        if (! $thumbnail) {
            return;
        }

        $this->hasimage = true;
        $this->thumbnailId = $thumbnail['id'];
        $this->thumbnailTitle = $thumbnail['title'];
        $this->thumbnailUrl = $thumbnail['url'];
        $this->thumbnailAlt = $thumbnail['alt'];
        $this->thumbnailDescription = $thumbnail['description'];
        $this->thumbnailCaption = $thumbnail['caption'];
        $this->thumbnailSizes = $thumbnail['sizes'];
        $this->makeSizes($thumbnail);
    }

    /**
     * @return int
     */
    public function id()
    {
        return $this->thumbnailId;
    }

    /**
     * @return string
     */
    public function identifier()
    {
        return "media-item-{$this->thumbnailId}";
    }

    /**
     * @return string
     */
    public function title()
    {
        return $this->thumbnailTitle;
    }

    /**
     * @param  string $size optional
     *
     * @return string
     */
    public function url($size = null)
    {
        if (! $size) {
            return $this->thumbnailUrl;
        }

        return $this->getSizes($size);
    }

    /**
     * @return string
     */
    public function alt()
    {
        return $this->thumbnailAlt;
    }

    /**
     * @return string
     */
    public function description($nl2br = false)
    {
        return $nl2br ? nl2br(rtrim($this->thumbnailDescription)) : $this->thumbnailDescription;
    }

    /**
     * @param  string $size
     *
     * @return string
     */
    public function getSizes($size)
    {
        return isset($this->thumbnailSizes[$size]) ? $this->thumbnailSizes[$size] : $this->thumbnailSizes['large'];
    }

    /**
     * @param string $size
     * @param array $attr optional
     *
     * @param bool $lazy
     *
     * @return string
     */
    public function render($size = null, $attr = [], $lazy = true)
    {
        if ($lazy) {
            if (isset($attr['class'])) {
                $attr['class'] .= ' lazy';
            } else {
                $attr['class'] = 'lazy';
            }
        }

        $srcset = wp_get_attachment_image_srcset($this->id(), $size);

        return $lazy
            ? '<img data-src="' . $this->url($size) . '" alt="' . $this->alt() . '" title="' . $this->title() . '"' . $this->makeAttr($attr) . ' data-srcset="'. $srcset .'" sizes="100vw">'
            : '<img src="' . $this->url($size) . '" alt="' . $this->alt() . '" title="' . $this->title() . '"' . $this->makeAttr($attr) . ' srcset="'. $srcset .'" sizes="100vw">';
    }

    /**
     * @return string
     */
    public function caption()
    {
        return $this->thumbnailCaption;
    }

    public function width()
    {
        return $this->thumbnailDimensions['default']['width'];
    }

    public function height()
    {
        return $this->thumbnailDimensions['default']['height'];
    }

    public function style($size = null)
    {
        if (! $srcset = wp_get_attachment_image_srcset($this->id(), $size)) {
            return "#{$this->identifier()} {background-image: url(".$this->url($size).")}";
            return 'background-image: url(' . $this->url($size) . ');';
        }

        $srcsets = explode(', ', $srcset);
        $currentSize = '';

        $css = [];

        foreach ($srcsets as $set) {
            $parts = explode(' ', $set);

            $url = esc_url($parts[0]);

            $imageTag = "#{$this->identifier()} {background-image: url({$url})}";

            if ($currentSize) {
                $css[] = "@media only screen and (min-width: {$currentSize}) { {$imageTag} }";
            } else {
                $css[] = $imageTag;
            }

            $currentSize = str_replace('w', 'px', $parts[1]);
        }

        return '<style>' . implode('', $css) . '</style>';
    }

    /**
     * @return boolean
     */
    public function exists()
    {
        return $this->hasimage;
    }

    private function makeAttr($attr = [])
    {
        $attributes = null;

        if (! count($attr)) {
            return null;
        }

        foreach ($attr as $attribute => $content) {
            $attributes .= " {$attribute}=\"{$content}\"";
        }

        return $attributes;
    }

    private function makeSizes($thumbnail)
    {
        $this->thumbnailDimensions['default'] = [
            'width' => $thumbnail['width'],
            'height' => $thumbnail['height']
        ];
    }

    public function __toString()
    {
        return $this->url();
    }
}