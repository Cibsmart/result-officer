<?php

declare(strict_types=1);

namespace App\Imports;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;

final class FinalResultsImport implements ToModel
{
    use Importable;

    /**
     * @param array<int, string> $row
     * {@inheritDoc}
     */
    public function model(array $row): void
    {
        dd($row);
    }
}
