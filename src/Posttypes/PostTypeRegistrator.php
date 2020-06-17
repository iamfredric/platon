<?php

namespace Platon\Posttypes;

class PostTypeRegistrator
{
    /**
     * @var array
     */
    protected $posttypes = [];

    /**
     * @param $slug
     *
     * @return \Platon\Posttypes\Posttype
     */
    public function register($slug)
    {
        return $this->posttypes[] = new Posttype($slug);
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
