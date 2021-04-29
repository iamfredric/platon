<?php

namespace PlatonTest\Unit\Models;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use Platon\Database\Model;
use Platon\Database\Paginaton;
use Platon\Database\QueryBuilder;
use Platon\Database\WpQuery;

class ModelTest extends TestCase
{
    use PHPMock;

    /** @test */
    function a_model_can_be_constructed()
    {
        $model = new Model($this->postArray());

        $this->assertEquals('Example post', $model->title);
        $this->assertEquals(8, $model->id);
    }

    /** @test */
    function the_current_model_can_be_fetched()
    {
        $mock = $this->getFunctionMock('Platon\\Database', 'get_post');
        $mock->expects($this->once())->willReturn($this->postArray());

        $model = Model::current();

        $this->assertEquals(8, $model->id);
    }

    /** @test */
    function a_model_can_be_fetched_via_id()
    {
        $mock = $this->getFunctionMock('Platon\\Database', 'get_post');
        $mock->expects($this->once())->with(8)->willReturn($this->postArray());
        $model = Model::find(8);

        $this->assertEquals(8, $model->id);
    }

    /** @test */
    function a_model_can_be_created()
    {
        $this->getFunctionMock('Platon\\Database', 'wp_insert_post')
            ->expects($this->once())
            ->with(['post_title' => 'Created example', 'post_type' => 'model'])
            ->willReturn($this->postArray());

        $this->getFunctionMock('Platon\\Database', 'get_post')
            ->expects($this->once())
            ->with($this->postArray())
            ->willReturn($this->postArray());

        $this->assertEquals(8, Model::create(['post_title' => 'Created example'])->id);
    }

    /** @test */
    function a_model_can_be_updated()
    {
        $this->getFunctionMock('Platon\\Database', 'wp_insert_post')
             ->expects($this->once())
             ->with(['post_title' => 'Updated title', 'ID' => 8, 'post_type' => 'model'])
             ->willReturn(['post_title' => 'Updated title', 'ID' => 8, 'post_type' => 'model']);

        $this->getFunctionMock('Platon\\Database', 'get_post')
             ->expects($this->once())
             ->with((['post_title' => 'Updated title', 'post_type' => 'model', 'ID' => 8]))
             ->willReturn($this->postArray(['post_title' => 'Updated title', 'post_type' => 'model']));

        $model = (new Model($this->postArray()))
            ->update(['title' => 'Updated title']);

        $this->assertEquals('Updated title', $model->title);
    }

    /** @test */
    function a_model_can_be_saved()
    {
        $this->getFunctionMock('Platon\\Database', 'wp_update_post')
             ->expects($this->once())
             ->with(($this->postArray(['post_title' => 'Me updated title'])));

        $model = new Model($this->postArray());

        $model->title = 'Me updated title';

        $model->save();

        $this->assertEquals('Me updated title', $model->title);
    }

    /** @test */
    public function a_model_can_be_paginated()
    {
        WpQuery::setInstance(function ($arguments) {
            return new class {
                public function __call($name, $arguments)
                {}
            };
        });

        $this->getFunctionMock('Platon\\Database', 'get_query_var')
             ->expects($this->once())
             ->with('paged')
             ->willReturn(0);

        $this->assertInstanceOf(Paginaton::class, Model::paginate(2));
    }

    public function all_models_can_be_fetched()
    {
        // all()
    }

    /** @test */
    function a_model_can_be_treated_as_an_array()
    {
        $model = new Model($this->postArray());

        $this->assertEquals(8, $model['id']);
    }

    /** @test */
    function a_model_can_be_casted_to_an_array()
    {
        $model = new Model($this->postArray());

        $this->assertEquals([
            "id" => 8,
            "comment_count" => "0",
            "comment_status" => "closed",
            "filter" => "raw",
            "guid" => "http://example.test/?page_id=6",
            "order" => 0,
            "ping_status" => "closed",
            "pinged" => "",
            "author" => "1",
            "content" => "",
            "content_filtered" => "",
            "date" => "2021-03-23 13:42:07",
            "excerpt" => "",
            "mime_type" => "",
            "modified" => "2021-04-19 08:52:56",
            "name" => "example-post",
            "parent" => 0,
            "password" => "",
            "status" => "publish",
            "title" => "Example post",
            "type" => "model",
            "to_ping" => ""],
            $model->toArray()
        );
    }

    /** @test */
    function a_model_can_be_casted_to_an_wordpress_array()
    {
        $this->assertEquals(
            $this->postArray(),
            (new Model($this->postArray()))->toWordpressArray()
        );
    }
    /** @test */
    function a_model_can_be_casted_to_json()
    {
        $this->assertEquals(
            json_encode([
                "id" => 8,
                "comment_count" => "0",
                "comment_status" => "closed",
                "filter" => "raw",
                "guid" => "http://example.test/?page_id=6",
                "order" => 0,
                "ping_status" => "closed",
                "pinged" => "",
                "author" => "1",
                "content" => "",
                "content_filtered" => "",
                "date" => "2021-03-23 13:42:07",
                "excerpt" => "",
                "mime_type" => "",
                "modified" => "2021-04-19 08:52:56",
                "name" => "example-post",
                "parent" => 0,
                "password" => "",
                "status" => "publish",
                "title" => "Example post",
                "type" => "model",
                "to_ping" => ""]
            ),
            (new Model($this->postArray()))->toJson()
        );

    }

    protected function postArray(array $merge = []): array
    {
        $data = array_merge([
            'ID' => 8,
            'post_author' => "1",
            'post_date' => "2021-03-23 13:42:07",
//            'post_date_gmt' => "2021-03-23 12:42:07",
            'post_content' => "",
            'post_title' => "Example post",
            'post_excerpt' => "",
            'post_status' => "publish",
            'comment_status' => "closed",
            'ping_status' => "closed",
            'post_password' => "",
            'post_name' => "example-post",
            'to_ping' => "",
            'pinged' => "",
            'post_modified' => "2021-04-19 08:52:56",
//            'post_modified_gmt' => "2021-04-19 06:52:56",
            'post_content_filtered' => "",
            'post_parent' => 0,
            'guid' => "http://example.test/?page_id=6",
            'menu_order' => 0,
            'post_type' => "model",
            'post_mime_type' => "",
            'comment_count' => "0",
            'filter' => "raw"
        ], $merge);

        ksort($data);

        return $data;
    }
}