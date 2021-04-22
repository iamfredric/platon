<?php

namespace Platon\Utilities\Support;

class Maps
{
    /**
     * @var array
     */
    protected $maps;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Maps constructor.
     *
     * @param array $maps
     * @param string $key
     * @param mixed $value
     */
    public function __construct($maps, $key, $value)
    {
        $this->maps = $maps;
        $this->key = $key;
        $this->value = $value;
    }

    public function value()
    {
        $mapable = null;

        foreach (array_keys($this->maps) as $map) {
            $keys = explode('.', $map);

            if ($keys[0] === $this->key) {
                $mapable = [
                    'key' => $map,
                    'keys' => trim(substr($map, strlen($keys[0])), '.')
                ];
                break;
            }
        }

        if (! $this->value) {
            return $this->value;
        }

        if (! $mapable) {
            return $this->value;
        }

        return collect($this->value)
            ->map(function ($item) use ($mapable) {
                return $this->extractValueFromKeys($item, $mapable['keys']);
            })
            ->mapInto($this->maps[$mapable['key']]);
    }

    public function extractValueFromKeys($value, $keys)
    {
        if ($keys) {
            foreach (explode('.', $keys) as $key) {
                $value = $value[$key];
            }
        }

        return $value;
    }
}