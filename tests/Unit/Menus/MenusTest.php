<?php

namespace PlatonTest\Unit\Menus;

use Platon\Menus\MenuRegistrator;
use PlatonTest\TestCase;

class MenusTest extends TestCase
{
    /** @test */
    function a_menu_can_be_registered()
    {
        $this->mockFunction('Platon\\Menus\\trans')
            ->willReturn('Test menu');

        $this->mockFunction('Platon.Menus.register_nav_menu')
            ->with('test-menu', 'Test menu');

        (new MenuRegistrator('do not need this'))->register('test-menu', 'Test menu');
    }

    /** @test */
    function a_menu_can_be_rendered()
    {
        $this->mockFunction('Platon.Menus.wp_nav_menu')
            ->with([
                'before' => 'test',
                'theme_location' =>  'me-testmenu',
                'container' => null,
                'items_wrap' => '%3$s'
            ]);

        (new MenuRegistrator('do not need this'))
            ->render('me-testmenu', [
                'before' => 'test'
            ]);
    }
}