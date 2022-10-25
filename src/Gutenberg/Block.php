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
    protected array $data = [];

    public function render($data, $content = '', $preview = false)
    {
        $previewImage = $data['data']['__preview_image'] ?? null;
        
        if ($preview && $previewImage) {
            echo "<img src=\"{$previewImage}\" >";
            return;
        }

        $this->data = get_fields($data['id']) ?: [];

        if ($preview && config('acf.blocks.preview_marking')) {
            echo '<div style="min-height: 40px; border-left: #eeeeee 4px solid; padding-left: 12px;margin-left: -12px;">';
        }
        echo view(
            str_replace(
                '{name}',
                $this->view(),
                config('paths.blocks', 'gutenberg.{$name}')
            ),
            array_merge($this->transform($this->data), [
                'preview' => $preview,
            ])
        );

        if ($preview && config('acf.blocks.preview_marking')) {
            echo '</div>';
        }
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

    protected function transform(?array $data)
    {
        $items = [];

        foreach ($data as $key => $value) {
            if (!str_starts_with($key, '_')) {
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

    public function data(?string $key = null, $default = null)
    {
        if ($key) {
            return $this->data[$key] ?? $default;
        }

        return $this->data;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name(),
            'title' => trans($this->title()),
            'description' => trans($this->description()),
            'render_callback' => [$this, 'render'],
            'category' => $this->category(),
            'icon' => $this->icon(),
            'keywords' => $this->keyWords(),
            'example' => $this->getExample(),
        ];
    }

    public function name(): string
    {
        return Str::slug($this->title());
    }

    abstract public function title(): string;

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

    protected function getExample(): ?array
    {
        if ($previewImage = $this->getPreviewImageUrl()) {
            return [
                'attributes' => [
                    'mode' => 'preview',
                    'data' => [
                        '__preview_image' => $previewImage,
                    ],
                ],
            ];
        }

        return null;
    }

    protected function getPreviewImageUrl(): ?string
    {
        if (empty(($dir = config('acf.blocks.preview_image_dir')))) {
            return null;
        }

        $path = implode('/', [theme_path($dir), $this->name() . '.jpg']);

        return file_exists($path)
            ? implode('/', [theme_url($dir), $this->name() . '.jpg'])
            : null;
    }

    public function previewImageUrl(): ?string
    {
        return null;
    }
}
