<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CreditUnit;
use Illuminate\Database\Eloquent\Model;

final class ProgramCurriculumSemester extends Model
{
    /** @return array{minimum_elective_units: 'App\Enums\CreditUnit'} */
    protected function casts(): array
    {
        return [
            'minimum_elective_units' => CreditUnit::class,
        ];
    }
}
