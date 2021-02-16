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
     * @var null | array
     */
    private $castables = null;

    /**
     * @param array $data
     *
     * @return array
     */
    protected function transform($data = [])
    {
        $items = [];

        foreach ($data as $key => $item) {
            $key = Str::camel($key);

            // Auto cast images to Image object
            $item = $this->massage($item, $key);

            $items[$key] = $item;

            // Appendix
            if (isset($this->append[$key])) {
                $appendKey = Str::camel($this->append[$key]);
                $methodName = "append". ucfirst($appendKey) . 'Attribute';
                $value = method_exists($this, $methodName) ? $this->$methodName($item) : null;
                $items[$appendKey] = $this->massage($value, $appendKey);
            }
        }

        return $items;
    }

        protected function massage($item, $key)
    {
        if (is_array($item) and isset($item['sizes'])) {
            $item = new Image($item);
        }

        if (is_array($item) && isset($item['url']) && isset($item['title']) && isset($item['target'])) {
            $item = new Link($item);
        }

        // Casts item if defined
        $item = $this->castItem($key, $item);

        // Maps item if it is defined
        $item = $this->mapItem($key, $item);

        // Transform attribute if defined
        $item = $this->transformItem($key, $item);

        return $item;
    }


    /**
     * Transforms the item if defined
     *
     * @param string $key
     * @param mixed $item
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
     * @param string $key
     * @param mixed $item
     *
     * @return mixed
     */
    protected function castItem($key, $item)
    {
        if (! $castable = $this->getCast($key)) {
            return $item;
        }

        if ($castable['type'] == 'array') {
            return $this->castArray($castable, $item);
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
     * @param mixed $cast
     * @param mixed $items
     *
     * @return array
     */
    protected function castArray($cast, $items)
    {
        return collect($items)->map(function ($item) use ($cast) {
            foreach ($cast['castables'] as $key => $castable) {
                $item[$key] = new $castable($item[$key]);
            }

            return $item;
        })->toArray();
    }

    /**
     * @param string $key
     * @param mixed $items
     *
     * @return array
     */
    protected function mapItem($key, $items)
    {
        if ( ! isset($this->map[$key])) {
            return $items;
        }

        if (! $items) {
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
            });
    }

    /**
     * @param string $requestedKey
     *
     * @return mixed|null
     */
    protected function getCast($requestedKey)
    {
        if (! is_array($this->castables)) {
            $this->castables = [];

            if ( ! count($this->casts)) {
                return null;
            }

            foreach ($this->casts as $key => $cast) {
                if (strpos($key, '.*.') > -1) {
                    [$key, $subkey] = explode('.*.', $key);
                } else {
                    $subkey = null;
                }

                if ( ! isset($this->castables[$key])) {
                    $this->castables[$key] = [
                        'type'      => $subkey ? 'array' : null,
                        'castables' => []
                    ];
                }

                if ($subkey) {
                    $this->castables[$key]['castables'][$subkey] = $cast;
                } else {
                    $this->castables[$key]['castables'][] = $cast;
                }
            }
        }

        return $this->castables[$requestedKey] ?? null;
    }
}
