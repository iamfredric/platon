<?php

namespace Platon\Utilities;

use Illuminate\Support\Str;
use Platon\Media\Link;
use Platon\Wordpress\Image;

trait Transformer
{
    /**
     * @var array
     */
    protected $map = [];

    /**
     * @var array
     */
    protected $casts = [];

    /**
     * @param array $data
     *
     * @return array
     */
    protected function transform($data = [])
    {
        $items = [];

        foreach ($data as $key => $item) {
            // Auto cast images to Image object
            if (is_array($item) and isset($item['sizes'])) {
                $item = new Image($item);
            }

            if (is_array($item) && isset($item['url']) && isset($item['title']) && isset($item['target'])) {
                $item = new Link($item);
            }

            $key = Str::camel($key);

            // Casts item if defined
            $item = $this->castItem($key, $item);

            // Maps item if it is defined
            $item = $this->mapItem($key, $item);

            // Transform attribute if defined
            $item = $this->transformItem($key, $item);

            $items[$key] = $item;
        }

        return $items;
    }

    /**
     * Transforms the item if defined
     *
     * @param $key
     * @param $item
     *
     * @return mixed
     */
    protected function transformItem($key, $item)
    {
        $methodname = "get" . ucfirst($key) . 'Attribute';

        if (method_exists($this, $methodname)) {
            return $this->{$methodname}($item);
        }

        return $item;
    }

    /**
     * @param $key
     * @param $item
     *
     * @return object
     */
    protected function castItem($key, $item)
    {
        if (! isset($this->casts[$key])) {
            return $item;
        }

        if (preg_match("/App\\\Models\\\(.*)/", $this->casts[$key])) {
            return $this->casts[$key]::make($item);
        }

        if ($this->casts[$key] == 'object') {
            return (object) $item;
        }

        return new $this->casts[$key]($item);
    }

    /**
     * @param $key
     * @param $items
     *
     * @return array
     */
    protected function mapItem($key, $items)
    {
        if ( ! isset($this->map[$key])) {
            return $items;
        }

        return collect($items)
            ->map(function ($item) use ($key) {
                if (preg_match("/App\\\Models\\\(.*)/", $this->map[$key])) {
                    return $this->map[$key]::make($item);
                }

                if ($this->map[$key] == 'object') {
                    return (object) $item;
                }

                return new $this->map[$key]($item);
            })
            ->toArray();
    }
}
