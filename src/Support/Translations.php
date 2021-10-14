<?php

namespace Platon\Support;

use Illuminate\Support\Collection;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class Translations
{
    protected Collection $files;

    protected Collection $strings;

    /**
     * @param array<string>|string $paths
     */
    public function __construct($paths)
    {
        $this->files = new Collection();
        $this->strings = new Collection();

        foreach ((array) $paths as $path) {
            $this->fetchFilesFromPath($path);
        }

        $this->files->each(fn ($file) => $this->extractStringsFromFile($file));

        $this->strings = $this->strings->unique()->values();
    }

    /**
     * @param array<string>|string $paths
     */
    public static function strings($paths): Collection
    {
        $instances = new static($paths);

        return $instances->toCollection();
    }

    protected function fetchFilesFromPath(string $path): void
    {
        foreach (new RegexIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path)
            ),
            '/^.+\.php$/i',
            RegexIterator::GET_MATCH
        ) as $files) {
            $this->files = $this->files->merge($files);
        }
    }

    public function extractStringsFromFile(string $file): void
    {
        $contents = file_get_contents($file);

        if (preg_match("/trans\('(.*?)'\)/i", $contents, $matches)) {
            $this->strings->push($matches[1]);
        }
    }

    public function toCollection(): Collection
    {
        return $this->strings;
    }
}
