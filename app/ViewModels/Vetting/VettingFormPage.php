<?php

declare(strict_types=1);

namespace App\ViewModels\Vetting;

use App\Data\Department\DepartmentListData;
use Spatie\LaravelData\Data;

final class VettingFormPage extends Data
{
    public function __construct(
        public readonly DepartmentListData $departments,
    ) {
    }
}
