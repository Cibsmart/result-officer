<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class FinalSemesterEnrollment extends Model
{
    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\FinalResult, \App\Models\FinalSemesterEnrollment>
     */
    public function finalResults(): HasMany
    {
        return $this->hasMany(FinalResult::class);
    }

    public function getResultCount(): int
    {
        return $this->finalResults->count();
    }

    public function getCreditUnitSum(): int
    {
        return (int) $this->finalResults->sum('credit_unit.value');
    }

    public function getGradePointSum(): int
    {
        return (int) $this->finalResults->sum('grade_point');
    }

    public function getGradePointAverage(): float
    {
        return round($this->getGradePointSum() / $this->getCreditUnitSum(), 3);
    }

    public function updateSumsAndAverage(): void
    {
        $this->credit_unit_sum = $this->getCreditUnitSum();
        $this->grade_point_sum = $this->getGradePointSum();
        $this->grade_point_average = $this->getGradePointAverage();
        $this->result_count = $this->getResultCount();
        $this->save();
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<int, float> */
    protected function gradePointAverage(): Attribute
    {
        return Attribute::make(
            get: static fn (int $value): float => $value / 1000,
            set: static fn (float $value): int => (int) ($value * 1000),
        );
    }
}
