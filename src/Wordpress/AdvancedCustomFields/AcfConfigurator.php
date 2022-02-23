<?php

namespace Platon\Wordpress\AdvancedCustomFields;

use Illuminate\Support\Collection;
use Exception;

class AcfConfigurator
{
    protected array $groups = [];

    public function register(): int
    {
        if (! count($this->groups)) {
            return 0;
        }

        if (! function_exists('register_extended_field_group')) {
            throw new Exception('wordplate/acf must be installed: composer require wordplate/acf');
        }

        foreach ($this->groups as $group) {
            register_extended_field_group((new $group())->toArray());
        }

        return 1;
    }

    public function group(string $name): static
    {
        $this->groups[] = $name;

        return $this;
    }

    public function groups(array $names): static
    {
        $this->groups = array_merge($this->groups, $names);

        return $this;
    }

    public function discoverGroups(string $namespace)
    {
        $path = trim($namespace, '\\');
        $path = str_replace('\\', '/', $path);
        $path = lcfirst($path);

        $this->groups = array_merge(
            $this->groups,
            (new Collection(scandir(theme_path($path))))
                ->filter(fn ($name) => str_ends_with($name, '.php'))
                ->map(fn ($name) => implode('\\', [$namespace, str_replace('.php', '', $name)]))
                ->values()
                ->toArray()
        );
    }
}
