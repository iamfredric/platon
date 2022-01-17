<?php

namespace PlatonTest\Unit\Posttypes;

use Platon\Posttypes\Taxonomy;
use PlatonTest\TestCase;

class TaxonomyTest extends TestCase
{
    /** @test */
    function a_taxonomy_can_be_configured_and_registered()
    {
        $this->mockFunction('Platon.Posttypes.trans', 16)
             ->willReturnArgument(0);

        $this->mockFunction('Platon.Posttypes.register_taxonomy')
            ->with('test-taxonomy', 'test-posttype', [
                'labels' => [
                    'name' => 'Testing',
                    'singular_name' => 'Test',
                    'search_items' => 'testing',
                    'popular_items' => 'Populära testing',
                    'all_items' => 'Alla testing',
                    'parent_item' => 'Föräldertest',
                    'parent_item_colon' => 'Föräldertest',
                    'edit_item' => 'Redigera test',
                    'view_item' => 'Visa test',
                    'update_item' => 'Uppdatera test',
                    'add_new_item' => 'Lägg till test',
                    'new_item_name' => 'Nytt test namn',
                    'add_or_remove_items' => 'Lägg till/ta bort test',
                    'choose_from_most_used' => 'Visa från mest använda testing',
                    'not_found' => 'Ingen test hittades',
                    'no_terms' => 'Inga testing'
                ],
                'query_var' => 'te',
                'description' => 'Me test taxonomy',
                'public' => true,
                'hierarchical' => true,
                'show_ui' => true,
                'show_tagcloud' => false,
                'show_in_rest' => false,
                'rewrite' => [
                    'slug' => 'test-tax'
                ]
            ]);

        (new Taxonomy('test-taxonomy'))
            ->slug('test-tax')
            ->singular('Test')
            ->plural('Testing')
            ->description('Me test taxonomy')
            ->multilevel()
            ->queryVar('te')
            ->register('test-posttype');
    }
}