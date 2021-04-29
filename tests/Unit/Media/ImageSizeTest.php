<?php

namespace PlatonTest\Unit\Media;

use PHPUnit\Framework\TestCase;
use Platon\Media\ImageSize;

class ImageSizeTest extends TestCase
{
    /** @test */
    function an_image_size_can_be_registered()
    {
        $size = new ImageSize('example-size');

        $size->width(200)->height(300);

        $this->assertEquals(200, $size->width);
        $this->assertEquals(300, $size->height);
        $this->assertTrue($size->crop);

        $size->scale();

        $this->assertFalse($size->crop);
    }

    /** @test */
    function width_and_height_can_be_set_by_name()
    {
        $size = new ImageSize('120x10');

        $this->assertEquals(120, $size->width);
        $this->assertEquals(10, $size->height);
    }
}