<?php

namespace PlatonTest\Unit\Routing;

use Platon\Routing\CustomRoute;

class CustomRouteTest extends \PlatonTest\TestCase
{
    /** @test */
    function a_custom_route_does_things()
    {
        $route = new CustomRoute('me-custom-route/{id}/{name}', 'CustomEndPoint@method');

        $this->assertEquals(
            'me-custom-routeidname', $route->id()
        );

        $this->assertFalse($route->isCallable());

        $this->assertEquals('CustomEndPoint', $route->getClassName());
        $this->assertEquals('method', $route->getMethodName());

        $this->assertEquals([
            'id', 'name'
        ], $route->getQueryVars()->values()->toArray());

        $this->assertEquals(
            'index.php?pagename=me-custom-routeidname&id=$matches[1]&name=$matches[2]',
            $route->getQuery()
        );

        $this->assertEquals(
            '^me-custom-route/([a-z0-9\-]+)/([a-z0-9\-]+)',
            $route->getRegex()
        );
    }
}