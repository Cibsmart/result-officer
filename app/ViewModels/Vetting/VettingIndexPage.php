<?php

declare(strict_types=1);

namespace App\ViewModels\Vetting;

use App\Data\Vetting\VettingEventGroupListData;
use Spatie\LaravelData\Data;

final class VettingIndexPage extends Data
{
    public function __construct(
        public readonly VettingEventGroupListData $data,
    ) {
    }
}
