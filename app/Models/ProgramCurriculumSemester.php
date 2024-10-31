<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CreditUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ProgramCurriculumSemester extends Model
{
    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumCourse, \App\Models\ProgramCurriculumSemester>
     */
    public function courses(): HasMany
    {
        return $this->HasMany(ProgramCurriculumCourse::class);
    }

    /** @return array{minimum_elective_units: 'App\Enums\CreditUnit'} */
    protected function casts(): array
    {
        return [
            'minimum_elective_units' => CreditUnit::class,
        ];
    }
}
