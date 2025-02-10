<?php

declare(strict_types=1);

namespace App\Helpers;

use FilesystemIterator;
use SplFileInfo;

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
            $className = $this->getClassName($file);

            if ($className === null || ! class_exists($className)) {
                continue;
            }

            $classes[] = $className;
        }

        return $classes;
    }

    private function getClassName(SplFileInfo|string $file): ?string
    {
        if (is_string($file)) {
            return null;
        }

        if (! $file->isFile() || $file->getExtension() !== 'php') {
            return null;
        }

        return $this->namespace . '\\' . pathinfo($file->getFilename(), PATHINFO_FILENAME);
    }
}
