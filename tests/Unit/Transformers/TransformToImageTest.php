<?php

namespace PlatonTests\Unit\Transformers;

use PHPUnit\Framework\TestCase;
use Platon\Support\Transformers\TransformToImage;
use Platon\Wordpress\Image;

class TransformToImageTest extends TestCase
{
    /** @test */
    function an_array_can_be_discovered_as_and_transformed_to_an_image()
    {
        $this->assertEquals('nothing to see here', (new TransformToImage('nothing to see here'))->transform());

        $this->assertEquals(['one', 'two', 'three'], (new TransformToImage(['one', 'two', 'three']))->transform());

        $image = [
            'id' => '',
            'url' => '',
            'alt' => '',
            'description' => '',
            'width' => '',
            'height' => '',
            'caption' => '',
            'title' => '',
            'sizes' => []
        ];

        $this->assertInstanceOf(Image::class, (new TransformToImage($image))->transform());
    }
}