<?php

namespace Platon\Database;

use Platon\Wordpress\Image;
use Platon\Wordpress\WpImage;

trait Thumbnail
{
    /**
     * @return \Platon\Wordpress\Image | \Platon\Wordpress\WpImage
     */
    public function getThumbnailAttribute()
    {
        if (! $this->attributes->has('thumbnail')) {
            $this->attributes->put('thumbnail', $this->localizeThumbnail());
        }

        return $this->attributes->get('thumbnail');
    }

    /**
     * @return \Platon\Wordpress\WpImage|\Platon\Wordpress\Image
     */
    protected function localizeThumbnail()
    {
        if (method_exists($this, 'getFieldsAttribute') && $this->fields->has('thumbnail')) {
            return new Image($this->fields->get('thumbnail'));
        }

        return new WpImage($this->id);
    }
}
