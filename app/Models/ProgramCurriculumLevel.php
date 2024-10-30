<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ProgramCurriculumLevel extends Model
{
    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumSemester> */
    public function semesters(): HasMany
    {
        return $this->HasMany(ProgramCurriculumSemester::class);
    }
}
