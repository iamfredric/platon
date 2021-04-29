<?php

namespace PlatonTest\Unit\Posttypes;

use Platon\Posttypes\Posttype;

class PosttypesTest extends \PlatonTest\TestCase
{
    /** @test */
    function a_post_type_can_be_configured_and_registered()
    {
        $this->mockFunction('Platon.Posttypes.trans', 16)
            ->willReturnArgument(0);

        $this->mockFunction('Platon.Posttypes.register_post_type')
            ->with('test-type', [
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_admin_bar' => true,
                'menu_position' => 1,
                'menu_icon' => 'test-icon',
                'can_export' => true,
                'delete_with_user' => false,
                'hierarchical' => false,
                'has_archive' => false,
                'query_var' => false,
                'capability_type' => 'testdummy',
                'show_in_rest' => true,
                'supports' => ['title', 'editor'],
                'rewrite' => [
                    'slug' => 'test-slug'
                ],
                'labels' => [
                    'name' => 'Test types',
                    'singular_name' => 'Test type',
                    'menu_name' => 'Test types',
                    'name_admin_bar' => 'Test types',
                    'add_new' => 'Lägg till test type',
                    'add_new_item' => 'Lägg till test type',
                    'edit_item' => 'Redigera test type',
                    'new_item' => 'Ny test type',
                    'view_item' => 'Visa test type',
                    'search_items' => 'Sök test type',
                    'not_found' => 'Inga test types hittades',
                    'not_found_in_trash' => 'Inga test types hittades i papperskorgen',
                    'all_items' => 'Alla test types',
                    'parent_item' => 'Parent test type',
                    'parent_item_colon' => 'Parent test type:',
                    'archive_title' => 'Test types'
                ]
            ]);

        (new Posttype('test-type'))
            ->plural('Test types')
            ->singular('Test type')
            ->supports('title', 'editor')
            ->icon('test-icon')
            ->slug('test-slug')
            ->useGutenberg()
            ->capability('testdummy')
            ->isExportable()
            ->position(1)
            ->register();
    }
}