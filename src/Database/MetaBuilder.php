<?php

namespace Platon\Database;

class MetaBuilder
{

    /**
     * @var array<\Platon\Database\MetaBuilder>
     */
    protected array $groups = [];

    protected array $arguments = [];

    protected string $relation = 'AND';

    public function where($key, $compare = null, $value = null): MetaBuilder
    {
        if (is_callable($key)) {
            return $this->buildGroup($key);
        }

        return $this->setArgument($key, $compare, $value);
    }

    public function whereNotNull($key): MetaBuilder
    {
        $this->arguments[] = [
            'key' => $key,
            'compare' => '!=',
            'value' => ''
        ];

        return $this;
    }

    public function orWhere(...$args): MetaBuilder
    {
        $this->relation = 'OR';

        return $this->where(...$args);
    }

    public function toArray(): array
    {
        $arguments = $this->arguments;

        foreach ($this->groups as $group) {
            $arguments = array_merge([$group->toArray()], $arguments);
        }

        if (count($arguments) > 1) {
            $arguments['relation'] = $this->relation;
        }

        return $arguments;
    }

    protected function buildGroup(callable $callable): MetaBuilder
    {
        $callable($group = new MetaBuilder());

        $this->groups[] = $group;

        return $this;
    }

    public function setArgument(string $key, ?string $compare, ?string $value = null): MetaBuilder
    {
        $this->arguments[] = [
            'key' => $key,
            'compare' => is_null($value) ? '=' : $compare,
            'value' => is_null($value) ? $compare : $value
        ];

        return $this;
    }
}
