<?php

namespace PlatonTest\Unit\Media;

use PHPUnit\Framework\TestCase;
use Platon\Media\Link;

class LinkTest extends TestCase
{
    /** @test */
    function a_link_can_be_rendered()
    {
        $link = new Link([
            'url' => 'https://example.com',
            'target' => '',
            'title' => 'Example'
        ]);

        $this->assertEquals(
            '<a href="https://example.com" class="example-class">Example</a>',
            $link->render('example-class')
        );
    }

    /** @test */
    function a_link_can_be_open_in_a_new_window()
    {
        $link = new Link([
            'url' => 'https://example.com',
            'target' => '_blank',
            'title' => 'Example'
        ]);

        $this->assertEquals(
            '<a href="https://example.com" class="example-class" target="_blank" rel="noopener">Example</a>',
            $link->render('example-class')
        );
    }
}