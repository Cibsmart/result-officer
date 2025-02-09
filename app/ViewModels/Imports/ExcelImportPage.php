<?php

declare(strict_types=1);

namespace App\ViewModels\Imports;

use App\Data\Imports\ExcelImportEventListData;
use Spatie\LaravelData\Data;

final class ExcelImportPage extends Data
{
    public function __construct(
        public readonly ExcelImportEventListData $data,
    ) {
    }
}
