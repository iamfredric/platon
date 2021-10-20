<?php

namespace Platon\Database;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Term
{
    protected Collection $attributes;

    protected array $hidden = [];

    protected array $dates = [];

    /**
     * @param \WP_Term $attributes
     */
    public function __construct($attributes = null)
    {
        $this->attributes = new Collection($attributes);
    }

    public static function all($args = [])
    {
        $instance = new static();

        return (new Collection(
            get_terms(array_merge($args, [
                'taxonomy' => $instance->type()
            ]))
        ))->mapInto(static::class);
    }

    public static function forModel(Model $model)
    {
        $instance = new static();

        return (new Collection(get_the_terms($model->id, $instance->type())))
            ->mapInto(static::class);
    }

    public function isActive()
    {
        return is_category($this->get('term_id')) || is_tax($this->type(), $this->get('term_id'));
    }

    public function getUrlAttribute()
    {
        return get_term_link($this->get('term_id'));
    }

    public function type(): string
    {
        if (isset($this->type)) {
            return $this->type;
        }

        $parts = explode('\\', get_class($this));

        return Str::snake(end($parts));
    }

    protected function attributeShouldBeHidden($key): bool
    {
        return in_array($key, $this->hidden);
    }

    protected function cast($key, $value)
    {
        if (isset($this->casts[$key])) {
            return new $this->casts[$key]($value);
        }

        return $value;
    }

    protected function getAttributeMethodName($key): string
    {
        return Str::of($key)
                  ->camel()
                  ->prepend('get')
                  ->append('Attribute')
                  ->__toString();
    }

    protected function translateKey(string $key): string
    {
        return [
            'id' => 'term_id',
            'title' => 'name'
        ][$key] ?? $key;
    }

    public function get($key)
    {
        if ($this->attributeShouldBeHidden($key)) {
            return null;
        }

        $key = $this->translateKey($key);

        $value = $this->attributes->get($key);

        $value = $this->cast($key, $value);

        if (method_exists($this, $method = $this->getAttributeMethodName($key))) {
            $value = $this->$method($value);
        }

        return $value;
    }

    public function __get($name)
    {
        if ($name == 'attributes') {
            return $this->attributes;
        }

        return $this->get($name);
    }
}
