<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class CSVFile
{
    public function __construct(public string $path)
    {
    }

    /** @return \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> */
    public function read(): Collection
    {
        $content = Storage::get($this->path);

        assert(! is_null($content));

        /** @var \Illuminate\Support\Collection<int, string> $lines */
        $lines = collect(explode("\n", $content));

        $headerLine = $lines->shift();

        assert(! is_null($headerLine));

        /** @var array<int, string> $header */
        $header = str_getcsv($headerLine);
        $header = collect($header)->map(fn (string $value) => Str::slug($value, '_'));

        /** @var \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> $collection */
        $collection = $lines->map(fn ($line) => $header->combine(str_getcsv($line)));

        return $collection;
    }
}
