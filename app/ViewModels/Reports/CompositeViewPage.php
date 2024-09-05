<?php

declare(strict_types=1);

namespace App\ViewModels\Reports;

use App\Data\Composite\CompositeSheetData;
use Spatie\LaravelData\Data;

final class CompositeViewPage extends Data
{
    public function __construct(
        public readonly CompositeSheetData $data,
    ) {
    }
}
