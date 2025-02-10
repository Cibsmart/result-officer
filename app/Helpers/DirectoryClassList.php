<?php

declare(strict_types=1);

namespace App\Helpers;

use FilesystemIterator;

final readonly class DirectoryClassList
{
    public function __construct(private string $directory, private string $namespace)
    {
    }

    public static function for(string $directory, string $namespace): self
    {
        return new self($directory, $namespace);
    }

    /** @return array<int, class-string> */
    public function get(): array
    {
        $classes = [];

        foreach (new FilesystemIterator($this->directory) as $file) {
            if (is_string($file)) {
                continue;
            }

            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $className = $this->namespace . '\\' . pathinfo($file->getFilename(), PATHINFO_FILENAME);

            if (! class_exists($className)) {
                continue;
            }

            $classes[] = $className;
        }

        return $classes;
    }
}
