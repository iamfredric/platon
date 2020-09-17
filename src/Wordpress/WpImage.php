<?php

namespace Platon\Wordpress;

class WpImage
{
    /**
     * @var int
     */
    protected $postId;

    /**
     * @var int
     */
    protected $thumbnailId;

    /**
     * WpImage constructor.
     *
     * @param int $postId
     */
    public function __construct($postId)
    {
        $this->postId = $postId;
    }

    /**
     * @return int
     */
    public function id()
    {
        if (! $this->thumbnailId) {
            $this->thumbnailId = get_post_thumbnail_id($this->postId);
        }

        return $this->thumbnailId;
    }

    /**
     * @return string
     */
    public function identifier()
    {
        return "thumbnail-{$this->thumbnailId}";
    }

    /**
     * @return string
     */
    public function title()
    {
        return get_the_title($this->id());
    }

    /**
     * @param string|null $size
     *
     * @return string
     */
    public function url($size = null)
    {
        return get_the_post_thumbnail_url($this->id(), $size);
    }

    /**
     * @param string $size
     * @param array $attr
     *
     * @return string
     */
    public function render($size = null, $attr = [])
    {
        return get_the_post_thumbnail($this->postId, $size, $attr);
    }

    /**
     * @param string|null $size
     *
     * @return string|null
     */
        public function style($size = null)
    {
        if (! $srcset = wp_get_attachment_image_srcset($this->id(), $size)) {
            return "#{$this->identifier()} {background-image: url(".$this->url($size).")}";
        }

        $srcsets = explode(', ', $srcset);
        $currentSize = '';

        $css = [];

        foreach ($srcsets as $set) {
            $parts = explode(' ', $set);

            $url = esc_url($parts[0]);

            $imageTag = "#{$this->identifier()} {background-image: url({$url})}";

            if ($currentSize) {
                $css[] = "@media only screen and (max-width: {$currentSize}) { {$imageTag} }";
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
        return has_post_thumbnail($this->postId);
    }
}
