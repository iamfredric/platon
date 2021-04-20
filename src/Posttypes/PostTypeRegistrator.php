<?php

namespace Platon\Posttypes;

/**
 * @mixin \Platon\Posttypes\Posttype
 */
class PostTypeRegistrator
{
    /**
     * @var array
     */
    protected $posttypes = [];

    /**
     * @param string $slug
     *
     * @return \Platon\Posttypes\Posttype
     */
    public function register($slug)
    {
        return $this->posttypes[] = new Posttype($slug);
    }

    /**
     * @param $id
     *
     * @return \Platon\Posttypes\Taxonomy
     */
    public function taxonomy($id)
    {
        return new Taxonomy($id);
    }

    /**
     * Registering those post types
     *
     * @return void
     */
    public function finalize()
    {
        foreach ($this->posttypes as $type) {
            $type->register();
        }
    }
}
