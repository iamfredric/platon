<?php

namespace Platon\Exceptions;

use Exception;
use WP_REST_Response;

class RestExceptionHandler
{
    public static function response(Exception $e)
    {
        if (method_exists($e, 'toRestResponse')) {
            return $e->toRestResponse();
        }

        if (method_exists($e, 'getErrors')) {
            return new WP_REST_Response([
                'errors' => $e->getErrors()
            ], $e->getCode());
        }

        return new WP_REST_Response([
            'errors' => $e->getMessage()
        ], $e->getCode());
    }
}