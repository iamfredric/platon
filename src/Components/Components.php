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
     * @var null
     */
    protected $prefix;

    /**
     * Components constructor.
     *
     * @param array $components
     * @param null $prefix
     */
    public function __construct($components = [], $prefix = null)
    {
        if ($prefix) {
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
     * @param $components
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
     * @param $name
     *
     * @return string
     */
    protected function resolveClassname($name)
    {
        $name = collect(explode('-', $name))->map(function ($name) {
            return ucfirst($name);
        })->implode('');

        if (! $this->prefix) {
            return (string) Str::of($name)->camel()->ucfirst()->prepend('\\App\\Components')->append('Component');
        }

        return (string) Str::of($name)->camel()->ucfirst()->prepend("\\App\\Components\\{$this->prefix}")->append('Component');
    }

    /**
     * @param $component
     * @param $classname
     *
     * @return \Incognito\Components\Component
     */
    protected function initializeComponent($component, $classname)
    {
        if (class_exists($classname)) {
            return new $classname($component, $this->prefix);
        }

        return new Component($component, $this->prefix);
    }
}
