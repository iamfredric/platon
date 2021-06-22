<?php

namespace Platon\Http;

use Illuminate\Support\Collection;
use Platon\Exceptions\CsrfException;

class Request
{
    /**
     * @var \WP_REST_Request
     */
    protected $request;

    protected $attributes;

    public function __construct($request)
    {
        $this->request = $request;
        $this->attributes = new Collection($request->get_params());
    }

    public function get($name, $default = null)
    {
        return $this->attributes->get($name, $default);
    }

    public function has($name): bool
    {
        return $this->attributes->has($name);
    }

    public function all()
    {
        return $this->attributes->all();
    }

    public function only(...$fields)
    {
        return $this->attributes->only($fields);
    }

    public function except(...$fields)
    {
        return $this->attributes->except($fields);
    }

    public function csrfToken()
    {
        return $this->request->get_header('X-Csrf-Token') ?: $this->get('_token');
    }

    public function protectAgainstCsrf()
    {
        if (csrf_token() !== $this->csrfToken()) {
            throw new CsrfException();
        }
    }

    public function __get($name)
    {
        return $this->get($name);
    }
}
