<?php

namespace PlatonTest\Unit\Models;

use Platon\Database\MetaBuilder;
use Platon\Database\Builder;
use PlatonTest\TestCase;

class BuilderTest extends TestCase
{
    /** @test */
    function a_meta_query_can_be_constructed()
    {
        $builder = new Builder();

        $builder->whereMeta(function (MetaBuilder $builder) {
            $builder->where('start_at', '<=', '2020-10-10 00:00:00')
                ->where('end_at', '>=', '2020-10-10 00:00:00');
        })->orWhereMeta('status', 'open');

        $this->assertEquals($builder->getArguments()['meta_query'], [
            'relation' => 'OR',
            [
                'relation' => 'AND',
                [
                    'key' => 'start_at',
                    'compare' => '<=',
                    'value' => '2020-10-10 00:00:00'
                ],[
                    'key' => 'end_at',
                    'compare' => '>=',
                    'value' => '2020-10-10 00:00:00'
                ]
            ],
            [
                'key' => 'status',
                'compare' => '=',
                'value' => 'open'
            ]
        ]);
    }

    /** @test */
    function another_or_query_can_be_constructed()
    {
        $builder = new Builder();

        $builder->whereMeta('test', true)->orWhereMeta('things', 'is-different');

        $this->assertEquals($builder->getArguments()['meta_query'], [
            'relation' => 'OR',
            [
                'key' => 'test',
                'compare' => '=',
                'value' => true
            ],[
                'key' => 'things',
                'compare' => '=',
                'value' => 'is-different'
            ]
        ]);
    }

    /** @test */
    function yet_another_one_can_be_constructed()
    {
        $builder = new Builder();

        $builder->whereMeta('things', 'works');

        $this->assertEquals($builder->getArguments()['meta_query'], [
            [
                'key' => 'things',
                'compare' => '=',
                'value' => 'works'
            ]
        ]);
    }
}
