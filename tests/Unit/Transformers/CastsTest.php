<?php

namespace PlatonTests\Unit\Transformers;

use PHPUnit\Framework\TestCase;
use Platon\Media\Link;
use Platon\Support\Transformers\Casts;
use stdClass;

class CastsTest extends TestCase
{
    /** @test */
    function an_item_can_be_casted_to_an_object()
    {
        $value = ['url' => 'Hej', 'title' => 'Waevva'];

        $this->assertInstanceOf(Link::class, (new Casts($value, Link::class))->transform());;

        $this->assertEquals('test', (new Casts('test'))->transform());
    }

    /** @test */
    function an_array_can_be_casted_to_a_stdclass()
    {
        $value = (new Casts(['title' => 'Example title', 'content' => 'Example content'], stdClass::class))
            ->transform();

        $this->assertInstanceOf(stdClass::class, $value);

        $this->assertEquals('Example title', $value->title);
        $this->assertEquals('Example content', $value->content);
    }

    /** @test */
    function an_object_can_be_casted_to_an_array()
    {
        $value = (new Casts((object) ['title' => 'Example title', 'content' => 'Example content'], 'array'))
            ->transform();

        $this->assertIsArray($value);

        $this->assertEquals('Example title', $value['title']);
        $this->assertEquals('Example content', $value['content']);
    }
}