<?php

namespace PlatonTest;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use PHPMock;

    protected function mockFunction(string $function, $times = 1): \PHPUnit\Framework\MockObject\Builder\InvocationMocker
    {
        $parts = preg_split("/[.\\\]/", $function);
        $functionName = array_pop($parts);

        return $this->getFunctionMock(implode('\\', $parts), $functionName)
                    ->expects(new InvokedCountMatcher($times));
    }
}