<?php

namespace PlatonTests\Unit\Transformers;

use PHPUnit\Framework\TestCase;
use Platon\Media\Link;
use Platon\Support\DotNotation;
use Platon\Support\Transformers\Caster;
use Platon\Support\Transformers\Casts;
use PlatonTest\Examples\ExampleComponent;

class TransformerTest extends TestCase
{
    /** @test */
    function an_item_can_be_casted()
    {
        $items = [
            'likeable' => ['url' => '#', 'title' => 'Me link']
        ];

        $casts = [
            'likeable' => Link::class
        ];

        $data = (new Caster($items, $casts))->transform();

        $this->assertInstanceOf(Link::class, $data['likeable']);
    }

    /** @test */
    function a_nested_item_can_be_casted()
    {
        $items = [
            'link' => [
                'item' => [
                    'url' => '#',
                    'title' => 'Me link'
                ]
            ]
        ];

        $casts = [
            'link.item' => Link::class
        ];

        $transformed = (new Caster($items, $casts))
            ->transform();

        $this->assertInstanceOf(Link::class, $transformed['link']);
    }

    /** @test */
    function a_nested_array_can_be_mapped_and_casted()
    {
        $items = [
            'links' => [
                [
                    'url' => '#',
                    'title' => 'One link'
                ],[
                    'url' => '#another-link',
                    'title' => 'Another link'
                ]
            ]
        ];

        $casts = [
            'links.*' => Link::class
        ];

        $transformed = (new Caster($items, $casts))
            ->transform();

        foreach ($transformed['links'] as $link) {
            $this->assertInstanceOf(Link::class, $link);
        }
    }

    /** @test */
    function an_nested_and_keyed_array_can_be_casted()
    {
        $items = [
            'links' => [
                [
                    'link' => [
                        'url' => '#',
                        'title' => 'One link'
                    ]
                ],[
                    'link' => [
                        'url' => '#another-link',
                        'title' => 'Another link'
                    ]
                ]
            ]
        ];


        $casts = [
            'links.*.link' => Link::class
        ];

        $transformed = (new Caster($items, $casts))
            ->transform();

        foreach ($transformed['links'] as $link) {
            $this->assertInstanceOf(Link::class, $link['link']);
        }
    }

    /** @test */
    function things_can_go_well()
    {
        $component = new ExampleComponent([
            'acf_fc_layout' => 'test',
            'nullable-to-be-transformed' => null,
            'prefixed' => 'hello',
            'casted' => ['url' => '#', 'title' => 'Link'],
            'very' => [
                ['nested' => ['url' => '#', 'title' => 'Link']],
                ['nested' => ['url' => '#', 'title' => 'Link']]
            ]
        ]);

        $this->assertEquals('i have been nullified', $component->data('nullable-to-be-transformed'));
        $this->assertEquals('very-much-hello', $component->data('prefixed'));

        $this->assertInstanceOf(Link::class, $component->data('casted'));

        foreach ($component->data('very') as $nested) {
            $this->assertInstanceOf(Link::class, $nested['nested']);
        }
    }
}
