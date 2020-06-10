<?php

namespace Platon\Database;

use ArrayAccess;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;

class Model implements Arrayable, Jsonable, ArrayAccess
{
    /**
     * Specified post type
     * If this is not set Modest resolve name via class name
     *
     * @var null|string
     */
    protected $type = null;

    /**
     * Keys that should cast to Carbon\Carbon instances
     *
     * @var array
     */
    protected $dates = [
        'date', 'modified'
    ];

    /**
     * Keys that should remain hidden
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Excerpt length in characters
     *
     * @var integer
     */
    protected $excerptLength = 120;

    /**
     * Attributes
     *
     * @var \Illuminate\Support\Collection
     */
    protected $attributes;

    /**
     * Create a new instance
     *
     * @param  \WP_Post $post
     *
     * @return self
     */
    public static function make(\WP_Post $post)
    {
        $instance = new static;

        $instance->setAttributes($post);

        return $instance;
    }

    /**
     * Create a new instance with current queried post
     *
     * @return self
     */
    public static function current()
    {
        return self::make(get_post());
    }

    /**
     * Create a new instance with given post id
     *
     * @param  integer $id
     *
     * @return Modest
     */
    public static function find($id)
    {
        return QueryBuilder::find($id, new static);
    }

    /**
     * Paginated results
     *
     * @param  integer $limit
     *
     * @return QueryBuilder
     */
    public static function paginate($limit = null)
    {
        return (new QueryBuilder(new static))->paginate($limit);
    }

    /**
     * @return mixed
     */
    public static function all()
    {
        return QueryBuilder::all(new static);
    }

    /**
     * Creates a new post in database
     *
     * @param  array  $params
     *
     * @return Modest
     */
    public static function create(array $params)
    {
        $instance = new static;

        $params['post_type'] = $instance->getType();

        $id = wp_insert_post($params);

        return self::find($id);
    }

    /**
     * Updates given post in database
     *
     * @param  array  $args
     *
     * @return Modest
     */
    public function update(array $args)
    {
        $params = [];

        foreach ($args as $key => $value) {
            $params[$this->translateAttributeKeyToWordpress($key)] = $value;
        }

        $params['ID'] = $this->id;

        return self::create($params);
    }

    /**
     * Saves current instances in database
     *
     * @return Modest
     */
    public function save()
    {
        return self::create($this->toWordpressArray());
    }

    /**
     * Getter for attributes
     *
     * @param $key
     *
     * @return mixed|null|static
     */
    public function get($key)
    {
        // If attribute is defined as hidden null is returned
        if ($this->attributeShouldBeHidden($key)) {
            return null;
        }

        $value = $this->attributes->get($key);

        // Filter value through the date casting method,
        // it only casts to dates if defined
        $value = $this->castToDates($key, $value);

        // If attribute getter is defined, the value gets filtered via this method
        if (method_exists($this, $method = $this->getAttributeMethodName($key))) {
            $value = $this->$method($value);
        }

        return $value;
    }

    /**
     * Excerpt attribute getter
     * The length is set by the excerptLength param
     *
     * @param $excerpt
     *
     * @return string
     */
    public function getExcerptAttribute($excerpt)
    {
        $this->getAttribute('excerpt', content_to_excerpt($this->content, $this->excerptLength));
    }

    /**
     * @param $key
     * @param null $value
     *
     * @return mixed
     */
    protected function getAttribute($key, $value = null)
    {
        if (! $this->attributes->has($key) && $value) {
            $this->attributes->put($key, $value);
        }

        return $this->attributes->get($key);
    }

    /**
     * Url attribute getter
     *
     * @param  null $url
     *
     * @return string
     */
    public function getUrlAttribute($url = null)
    {
        $this->getAttribute('url', get_permalink($this->id));
    }

    /**
     * Translates key name to attribute getter name
     *
     * @param $key
     *
     * @return string
     */
    protected function getAttributeMethodName($key)
    {
        $key = Str::camel($key);

        return "get{$key}Attribute";
    }

    /**
     * Determines post type based on defined type or class name
     *
     * @return string
     */
    public function getType()
    {
        if ($this->type) {
            return $this->type;
        }

        $reflection = new ReflectionClass($this);

        return Str::camel($reflection->getShortName());
    }

    /**
     * Casts defined key values to carbon instances
     *
     * @param $key
     * @param $value
     *
     * @return Carbon
     */
    protected function castToDates($key, $value)
    {
        if (! in_array($key, $this->dates)) {
            return $value;
        }

        return \Carbon\Carbon::parse($value);
    }

    /**
     * Setter for attributes
     *
     * @param $attributes
     */
    public function setAttributes($attributes)
    {
        $collection = [];

        foreach ($attributes as $key => $value) {
            $key = $this->translateAttributeKey($key);

            if (preg_match("/_gmt/", $key)) {
                continue;
            }

            $collection[$key] = $value;
        }

        $this->attributes = new Collection($collection);
    }

    /**
     * Checks if given attribute key should be hidden
     *
     * @param $key
     *
     * @return bool
     */
    protected function attributeShouldBeHidden($key)
    {
        if (in_array($key, $this->hidden)) {
            return true;
        }

        return false;
    }

    /**
     * Translates attribute keys from Wordpress to Modest
     *
     * @param $key
     *
     * @return string
     */
    protected function translateAttributeKey($key)
    {
        return (string) Str::of($key)->lower()->replace(['post_', 'menu_'], '');
    }

    /**
     * Translates attribute keys from Modest to Wordpress
     * @param $key
     *
     * @return string
     */
    public function translateAttributeKeyToWordpress($key)
    {
        if ($key == 'id') {
            return Str::upper($key);
        }

        if ($key == 'order') {
            return "menu_{$key}";
        }

        if (in_array($key, ['comment_status', 'ping_status', 'comment_count', 'menu_order'])) {
            return $key;
        }

        return "post_{$key}";
    }

    /**
     * Casts all attributes to an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes->except($this->hidden)->toArray();
    }

    /**
     * Casts alla attributes to an Wordpress array
     *
     * @return array
     */
    public function toWordpressArray()
    {
        $items = [];

        foreach ($this->attributes as $key => $value) {
            $items[$this->translateAttributeKeyToWordpress($key)] = $value;
        }

        return $items;
    }

    /**
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return $this->attributes->except($this->hidden)->toJson();
    }

    /**
     * Determines whether a offset exists
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return ! is_null($this->get($offset));
    }

    /**
     * Sets offset to retrieve
     *
     * @param mixed $offset
     *
     * @return mixed|null|Modest
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param $method
     * @param $value
     *
     * @return mixed
     */
    public function __set($method, $value)
    {
        return $this->attributes[$method] = $value;
    }

    /**
     * @param $method
     *
     * @return \Illuminate\Support\Collection|mixed|null|Modest
     */
    public function __get($method)
    {
        if ($method == 'attributes') {
            return $this->attributes;
        }

        return $this->get($method);
    }

    /**
     * @param $method
     * @param $args
     *
     * @return \Wordpriest\Modest\QueryBuilder
     */
    public static function __callStatic($method, $args)
    {
        $instance = new static;

        return (new QueryBuilder($instance))->__call($method, $args);
    }
}
