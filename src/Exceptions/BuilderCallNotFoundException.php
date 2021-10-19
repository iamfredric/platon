<?php

namespace Platon\Exceptions;

use Throwable;

class BuilderCallNotFoundException extends \Exception
{
    public static function methodNotFound($method, $args)
    {
        return new static("The query builder call to {$method} was not found");
    }
}