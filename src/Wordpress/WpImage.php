<?php

namespace Platon\Wordpress;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class WpImage implements Arrayable, Jsonable
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
        return wp_get_attachment_image_url($this->id(), $size);
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
            return "<style>#{$this->identifier()} {background-image: url(".$this->url($size).")}</style>";
        }

        $css = collect(explode(', ', $srcset))->map(function ($item) {
            [$url, $width] = explode(' ', $item);

            return (object) [
                'url' => $url,
                'width' => (int) str_replace("w", "", $width)
            ];
        })->sortByDesc('width')->map(function ($item) {
            return "@media only screen and (max-width: {$item->width}px) { #{$this->identifier()} {background-image: url({$item->url})} }";
        })->implode('');

        return "<style>#{$this->identifier()} {background-image: url(".$this->url($size).")}{$css}</style>";
    }

    /**
     * @param string|null $size
     *
     * @return string
     */
    public function styles($size = null)
    {
        if ($style = $this->style($size)) {
            add_action('wp_footer', function () use($style) {
                echo $style;
            });

            return "id={$this->identifier()}";
        }
    }

    /**
     * @return boolean
     */
    public function exists()
    {
        return has_post_thumbnail($this->postId);
    }

    public function toArray(): array
    {
        return acf_get_attachment($this->id()) ?: [];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
