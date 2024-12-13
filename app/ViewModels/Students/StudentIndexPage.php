<?php

declare(strict_types=1);

namespace App\ViewModels\Students;

use App\Data\Students\StudentListPaginatedData;
use Spatie\LaravelData\Data;

final class StudentIndexPage extends Data
{
    public function __construct(
        public readonly StudentListPaginatedData $data,
    ) {
    }
}
