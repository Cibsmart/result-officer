<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramCurriculumLevel extends Model
{
    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ProgramCurriculumSemester> */
    public function semesters(): HasMany
    {
        return $this->HasMany(ProgramCurriculumSemester::class);
    }
}
