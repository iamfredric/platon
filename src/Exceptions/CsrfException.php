<?php

namespace Platon\Exceptions;

use Throwable;

class CsrfException extends \Exception
{
    public function __construct($message = "Session expired", $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}