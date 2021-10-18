<?php

namespace Platon\Gutenberg;

use Illuminate\Support\Str;
use Platon\Support\Transformers\AttributeGetters;
use Platon\Support\Transformers\AttributesWhenNull;
use Platon\Support\Transformers\AutoCaster;
use Platon\Support\Transformers\Caster;
use Platon\Support\Transformers\MapKeysToCamel;
use Platon\Support\Transformers\Transformations;

abstract class Block
{
    abstract public function title(): string;

    public function render($data)
    {
        echo view(
            str_replace('{name}', $this->view(), config('paths.blocks', 'gutenberg.{$name}')),
            $this->transform($data)
        );
    }

    protected function transform(?array $data)
    {
        $items = [];

        foreach ($data['data'] as $key => $value) {
            if (! str_starts_with($key, '_')) {
                $items[$key] = $value;
            }
        }

        return (new Transformations($items))
            ->through(Caster::class, $this->casts ?? [])
            ->through(AutoCaster::class)
            ->through(AttributeGetters::class, $this)
            ->through(AttributesWhenNull::class, $this)
            ->through(MapKeysToCamel::class)
            ->output();
    }

    public function view(): string
    {
        $items = explode('\\', get_class($this));

        $item = end($items);

        if (str_ends_with($item, 'Block')) {
            $item = substr($item, 0, strlen($item) - 5);
        }

        return Str::kebab($item);
    }

    public function name(): string
    {
        return Str::slug($this->title());
    }

    public function description(): ?string
    {
        return null;
    }

    public function category(): ?string
    {
        return null;
    }

    public function icon(): ?string
    {
        return null;
    }

    public function keyWords(): array
    {
        return [];
    }

    public function toArray(): array
    {
        return [
            'name'              => $this->name(),
            'title'             => trans($this->title()),
            'description'       => trans($this->description()),
            'render_callback' => [$this, 'render'],
            'category'          => $this->category(),
            'icon'              => $this->icon(),
            'keywords'          => $this->keyWords(),
        ];
    }
}
