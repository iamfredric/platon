<?php

namespace Platon\Gutenberg;

use Platon\Application;

class Gutenberg
{
    /**
     * @var \Platon\Application
     */
    protected Application $app;

    protected array $blocks = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function block(string $block): Gutenberg
    {
        $this->blocks[] = $block;

        return $this;
    }

    public function finalize(): void
    {
        if (! function_exists('acf_register_block_type')) {
            return;
        }

        foreach ($this->blocks as $abstract) {
            $concrete = $this->app->make($abstract);

            $array = array_merge($concrete->toArray(), [
                'render_callback' => [$concrete, 'render']
            ]);

            acf_register_block_type($array);
        }
    }
}