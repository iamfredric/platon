<?php

namespace Platon\Components;

use Platon\Utilities\Transformer;

class Component
{
    use Transformer;

    /**
     * @var string
     */
    protected $view = '';

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Component constructor.
     *
     * @param $data
     * @param null $prefix
     */
    public function __construct($data, $prefix = null)
    {
        $this->view = $data['acf_fc_layout'];

        if ($prefix) {
            $this->prefix = strtolower($prefix);
        }

        unset($data['acf_fc_layout']);

        $this->data = $this->transform($data);
    }

    /**
     * Renders the component
     *
     * @return \Jenssegers\Blade\Blade
     */
    public function render()
    {
        if ($this->prefix) {
            return view("components.{$this->prefix}.{$this->view}", $this->data);
        }

        return view("components.{$this->view}", $this->data);
    }
}
