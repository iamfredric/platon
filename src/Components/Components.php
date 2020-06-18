<?php

namespace Platon\Components;

use Illuminate\Support\Str;

class Components
{
    /**
     * @var array
     */
    protected $components = [];

    /**
     * @var string|null
     */
    protected $prefix;

    /**
     * Components constructor.
     *
     * @param array $components
     * @param string|null $prefix
     */
    public function __construct($components = [], $prefix = null)
    {
        if (! is_null($prefix)) {
            $this->prefix = ucfirst(strtolower($prefix));
        }

        $this->resolveComponents($components);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->components;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return count($this->components) > 0;
    }

    /**
     * @param array $components
     *
     * @return void
     */
    protected function resolveComponents($components)
    {
        if (! $components) {
            return;
        }

        foreach ($components as $component) {
            $this->components[] = $this->initializeComponent(
                $component,
                $this->resolveClassname($component['acf_fc_layout'])
            );
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function resolveClassname($name)
    {
        $name = collect(explode('-', $name))->map(function ($name) {
            return ucfirst($name);
        })->implode('');

        if (! $this->prefix) {
            return (string) Str::of($name)->camel()->ucfirst()->prepend('\\App\\Components\\')->append('Component');
        }

        return (string) Str::of($name)->camel()->ucfirst()->prepend("\\App\\Components\\{$this->prefix}")->append('Component');
    }

    /**
     * @param array $component
     * @param string $classname
     *
     * @return \Platon\Components\Component
     */
    protected function initializeComponent($component, $classname)
    {
        if (class_exists($classname)) {
            return new $classname($component, $this->prefix);
        }

        return new Component($component, $this->prefix);
    }
}
