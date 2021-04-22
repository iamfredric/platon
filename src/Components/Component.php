<?php

namespace Platon\Components;

use Illuminate\Support\Str;
use Platon\Support\Transformers\AttributeGetters;
use Platon\Support\Transformers\AttributesWhenNull;
use Platon\Support\Transformers\AutoCaster;
use Platon\Support\Transformers\Caster;
use Platon\Support\Transformers\MapKeysToCamel;
use Platon\Support\Transformers\Transformations;

class Component
{
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
     * @var array
     */
    protected $casts = [];

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

        $this->data = (new Transformations($data))
            ->through(Caster::class, $this->casts ?? [])
            ->through(AutoCaster::class)
            ->through(AttributeGetters::class, $this)
            ->through(AttributesWhenNull::class, $this)
            ->through(MapKeysToCamel::class)
            ->output();

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

    public function data($key = null)
    {
        if ($key) {
            return $this->data[Str::camel($key)];
        }

        return $this->data;
    }
}
