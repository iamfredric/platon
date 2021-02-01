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
     * @var string|null
     */
    protected $prefix;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Component constructor.
     *
     * @param array $data
     * @param string|null $prefix
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
     * @return \Jenssegers\Blade\Blade
     *
     * @throws \ReflectionException
     */
    public function render()
    {
        if ($path = config('paths.components')) {
            $view = str_replace('{name}', $this->prefix ? "{$this->prefix}.{$this->view}" : $this->view, $path);
        } else {
            $view = $this->prefix ? "components.{$this->prefix}.{$this->view}" : "components.{$this->view}";
        }

        return view($view, $this->data);
    }
}
