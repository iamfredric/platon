<?php

namespace Platon\Routing;

use Illuminate\Support\Collection;

class ApiRoute
{
    protected $method;

    protected $uri;

    protected $endpoint;

    public function __construct($method, $uri, $endpoint)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->endpoint = $endpoint;
    }

    public function method()
    {
        return $this->method;
    }

    public function uri()
    {
        if (str_contains($this->uri, '{')) {
            return collect(explode('/', $this->uri))->map(function ($part) {
                if (str_contains($part, '{')) {
                    $name = str_replace(['{', '}'], '', $part);

                    return "(?P<{$name}>\w+)";
                }

                return $part;
            })->implode('/');
        }

        return $this->uri;
    }

    public function endpoint()
    {
        return $this->endpoint;
    }

    public function isCallable()
    {
        if (is_array($this->endpoint)) {
            return false;
        }

        return is_callable($this->endpoint);
    }

    public function getClassName()
    {
        if (! is_array($this->endpoint)) {
            if (strpos($this->endpoint, '@') > -1) {
                [$classname] = explode('@', $this->endpoint);

                return $classname;
            }

            if (strpos($this->endpoint, '.') > -1) {
                [$classname] = explode('.', $this->endpoint);

                return $classname;
            }

            return $this->endpoint;
        }

        return $this->endpoint[0];
    }

    public function getMethodName()
    {
        if (! is_array($this->endpoint)) {
            if (strpos($this->endpoint, '@') > -1) {
                [$classname, $methodname] = explode('@', $this->endpoint);

                return $methodname;
            }

            if (strpos($this->endpoint, '.') > -1) {
                [$classname, $methodname] = explode('.', $this->endpoint);

                return $methodname;
            }

            return '__invoke';
        }

        return $this->endpoint[1] ?? null;
    }

    public function getUriParams(): array
    {
        return (new Collection(explode('/', $this->uri)))
            ->filter(fn ($part) => str_contains($part, '{'))
            ->map(fn ($part) => str_replace(['{', '}'], '', $part))
            ->toArray();
    }
}