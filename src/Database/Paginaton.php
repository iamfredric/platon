<?php

namespace Platon\Database;

class Paginaton
{
    public $items;

    protected $query;

    public function __construct($items, $query)
    {
        $this->items = $items;
        $this->query = $query;
    }

    public function paginate($args = [])
    {
        return paginate_links(array_merge([
            'total'              => $this->query->max_num_pages,
            'current'            => get_query_var('paged') ?: 1
        ], $args));
    }

    public function prev($label = 'Next')
    {
        return get_previous_posts_link($label);
    }

    public function next($label = 'Next')
    {
        return get_next_posts_link($label);
    }

    public function currentPage()
    {
        return get_query_var('paged') ?: 1;
    }

    public function maxPage()
    {
        return $this->query->max_num_pages;
    }

    public function toArray()
    {
        return $this->items;
    }

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
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}
