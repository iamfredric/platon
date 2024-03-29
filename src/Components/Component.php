<?php

namespace Platon\Components;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Platon\Support\Transformers\AttributeGetters;
use Platon\Support\Transformers\AttributesWhenNull;
use Platon\Support\Transformers\AutoCaster;
use Platon\Support\Transformers\Caster;
use Platon\Support\Transformers\MapKeysToCamel;
use Platon\Support\Transformers\Transformations;
use ReflectionClass;
use ReflectionMethod;

class Component implements Arrayable, Jsonable
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

    protected ?string $nextComponent = null;

    protected ?string $prevComponent = null;

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

        $this->data = $this->appendDataAttributes($data);

        $this->data = (new Transformations($this->data))
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

        return view($view, $this->attributes());
    }

    public function data($key = null)
    {
        if ($key) {
            return $this->data[Str::camel($key)] ?? null;
        }

        return $this->data;
    }

    public function attributes()
    {
        return array_merge($this->data, [
            'nextComponent' => $this->nextComponent,
            'prevComponent' => $this->prevComponent,
            'currentComponent' => $this->hash(),
        ]);
    }

    protected function appendDataAttributes(array $data)
    {
        $reflection = new ReflectionClass($this);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (str_starts_with($method->getName(), 'append') && str_ends_with($method->getName(), 'Attribute')) {
                $key = strtolower(str_replace(['append', 'Attribute'], '', $method->getName()));

                $data[$key] = $this->{$method->getName()}();
            }
        }

        return $data;
    }

    public function view()
    {
        return $this->view;
    }

    public function toArray(): array
    {
        return [
            'view' => $this->view,
            'data' => (new Collection($this->data))
                ->mapWithKeys(function ($value, $key) {
                    if (is_array($value)) {
                        $value = (new Collection($value));
                    }

                    return [$key => $value instanceof Arrayable ? $value->toArray() : $value];
                })
        ];
    }

    public function setPreviousComponent(?string $hash)
    {
        $this->prevComponent = $hash;
    }

    public function setNextComponent(?string $hash)
    {
        $this->nextComponent = $hash;
    }

    public function hash(): string
    {
        return $this->view;
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
