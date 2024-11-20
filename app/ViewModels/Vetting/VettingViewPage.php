<?php

declare(strict_types=1);

namespace App\ViewModels\Vetting;

use App\Data\Vetting\VettingListData;
use Spatie\LaravelData\Data;

final class VettingViewPage extends Data
{
    public function __construct(
        public readonly VettingListData $data,
    ) {
    }
}
