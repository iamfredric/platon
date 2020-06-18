<?php

namespace Platon\Database;

class Paginaton
{
    /**
     * @var array
     */
    public $items;

    /**
     * @var \WP_Query
     */
    protected $query;

    /**
     * Paginaton constructor.
     *
     * @param array $items
     * @param \WP_Query $query
     */
    public function __construct($items, $query)
    {
        $this->items = $items;
        $this->query = $query;
    }

    /**
     * @param array $args
     *
     * @return mixed
     */
    public function paginate($args = [])
    {
        return paginate_links(array_merge([
            'total'              => $this->query->max_num_pages,
            'current'            => get_query_var('paged') ?: 1
        ], $args));
    }

    /**
     * @param string $label
     *
     * @return mixed
     */
    public function prev($label = 'Next')
    {
        return get_previous_posts_link($label);
    }

    /**
     * @param string $label
     *
     * @return mixed
     */
    public function next($label = 'Next')
    {
        return get_next_posts_link($label);
    }

    /**
     * @return int
     */
    public function currentPage()
    {
        return get_query_var('paged') ?: 1;
    }

    /**
     * @return int
     */
    public function maxPage()
    {
        return $this->query->max_num_pages;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
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
        return isset($this->items[$offset]);
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
        return $this->items[$offset];
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
        $this->items[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}
