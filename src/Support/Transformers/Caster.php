<?php

namespace Platon\Support\Transformers;

class Caster
{
    /**
     * @var mixed
     */
    protected $values;

    /**
     * @var array
     */
    protected $casts;

    /**
     * Caster constructor.
     *
     * @param array $values
     * @param array $casts
     */
    public function __construct($values, $casts)
    {
        $this->values = $values;
        $this->casts = $casts;
    }

    /**
     * @return array|mixed
     */
    public function transform()
    {
        foreach ($this->casts as $key => $cast) {
            $keys = explode('.', $key);
            $key = array_shift($keys);

            $this->values[$key] = $this->transformItem($key, $keys, $cast);
        }

        return $this->values;
    }

    /**
     * @param $key
     * @param $keys
     * @param $cast
     *
     * @return array|array[]|mixed|object|object[]
     */
    protected function transformItem($key, $keys, $cast)
    {
        $value = $this->values[$key];
        $multiple = false;

        if (count($keys)) {
            while ($index = array_shift($keys)) {
                if ($index === '*' && count($keys) > 0) {
                    $multiple = true;
                    continue;
                }

                if ($multiple) {
                    $value = array_map(function ($value) use ($index, $keys, $cast) {
                        return [$index => $this->cast($value, $index, $keys, $cast)];
                    }, $value);

                    $multiple = false;
                } else {
                    $value = $this->cast($value, $index, $keys, $cast);
                }
            }
        } else {
            return (new Casts($value, $cast))->transform();
        }

        return $value;
    }

    /**
     * @param $value
     * @param $key
     * @param $keys
     * @param $cast
     *
     * @return array|array[]|mixed|object|object[]
     */
    protected function cast($value, $key, $keys, $cast, $multiple = false)
    {
        if ($key === '*' && count($keys) === 0) {
            return array_map(function ($value) use ($cast) {
                return (new Casts($value, $cast))->transform();
            }, $value);
        } elseif ($key === '*' && count($keys) > 0) {
            $key = array_shift($keys);
            return array_map(function ($value) use ($cast, $key, $keys) {
                return $this->cast($value, $key, $keys, $cast, true);
            }, $value);
        };

        return (new Casts($value, $cast))->transform();
    }
}
