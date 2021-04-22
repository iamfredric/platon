<?php

namespace PlatonTests\Unit\Transformers;

use PHPUnit\Framework\TestCase;
use Platon\Media\Link;
use Platon\Support\Transformers\TransformToLink;

class TransformToLinkTest extends TestCase
{
    /** @test */
    function an_array_can_be_discovered_and_transformed_to_a_link_()
    {
        $this->assertEquals('test', (new TransformToLink('test'))->transform());
        $this->assertEquals(['url' => '#'], (new TransformToLink(['url' => '#']))->transform());

        $this->assertInstanceOf(Link::class, (new TransformToLink(['url' => '#', 'title' => 'Example link', 'target' => '']))->transform());
    }
}