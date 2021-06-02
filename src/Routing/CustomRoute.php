<?php

namespace Platon\Routing;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CustomRoute
{
    protected $uri;

    protected $endpoint;

    public function __construct($uri, $endpoint)
    {
        $this->uri = $uri;
        $this->endpoint = $endpoint;
    }

    public function id()
    {
        return Str::slug($this->uri);
    }

    public function getRegex()
    {
        $uri = (new Collection(explode('/', $this->uri)))
            ->map(fn($part) => str_starts_with($part, '{')
                ? '([a-z0-9\-]+)'
                : $part)
            ->implode('/');

        return "^{$uri}";
    }

    public function getQuery()
    {
        $arguments = [
            'pagename='. $this->id()
        ];

        $number = 1;
        $query = (new Collection(explode('/', $this->uri)))
            ->skip(1);

        foreach ($query as $key) {
            if (str_starts_with($key, '{')) {
                $key = str_replace(['{', '}'], '', $key);
                $arguments[] = $key . '=' . '$matches['.$number.']';
                $number++;
            } else {
                $arguments[] = $key;
            }
        }

        return 'index.php?' . implode('&', $arguments);
    }

    public function getQueryVars()
    {
        return (new Collection(explode('/', $this->uri)))
            ->filter(fn($part) => str_starts_with($part, '{'))
            ->map(fn($part) => str_replace(['{', '}'], '', $part));
    }

    /**
     * @return bool
     */
    public function isCallable()
    {
        if (is_array($this->endpoint)) {
            return false;
        }

        return is_callable($this->endpoint);
    }

    /**
     * @return mixed|string
     */
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

    /**
     * @return string|null
     */
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

    public function getCallable()
    {
        return $this->endpoint;
    }
}
