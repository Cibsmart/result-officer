<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ComputationStrategy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class FinalStudent extends Model
{
    /** @param array<string, array<string, string>> $data */
    public static function fromStudent(Student $student, array $data): self
    {
        $finalStudent = new self();
        $data = [...$data, 'student_id' => $student->id];

        $finalStudent->fill($data)->save();

        return $finalStudent;
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Student, \App\Models\FinalStudent> */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\FinalSessionEnrollment, \App\Models\FinalStudent>
     */
    public function finalSessionEnrollments(): HasMany
    {
        return $this->hasMany(FinalSessionEnrollment::class);
    }

    public function getResultCount(): int
    {
        return $this->finalSessionEnrollments->sum('result_count');
    }

    public function getCreditUnitSum(): int
    {
        return $this->finalSessionEnrollments->sum('credit_unit_sum');
    }

    public function getGradePointSum(): int
    {
        return $this->finalSessionEnrollments->sum('grade_point_sum');
    }

    public function getCumulativeGradePointAverageSum(): float
    {
        return $this->finalSessionEnrollments->sum('cummulative_grade_point_average');
    }

    public function getFinalCumulativeGradePointAverage(): float
    {
        if ($this->finalSessionEnrollments->count() === 0 && $this->getCreditUnitSum() === 0) {
            return 0.000;
        }

        if (Institution::first()->strategy === ComputationStrategy::SEMESTER) {
            return round($this->getCumulativeGradePointAverageSum() / $this->finalSessionEnrollments->count(), 3);
        }

        return round($this->getGradePointSum() / $this->getCreditUnitSum(), 3);
    }

    public function updateCountSumAndAverages(): void
    {
        $this->credit_unit_sum = $this->getCreditUnitSum();
        $this->grade_point_sum = $this->getGradePointSum();
        $this->cummulative_grade_point_average_sum = $this->getCumulativeGradePointAverageSum();
        $this->final_cummulative_grade_point_average = $this->getFinalCumulativeGradePointAverage();
        $this->result_count = $this->getResultCount();
        $this->save();
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<int, float> */
    protected function cummulativeGradePointAverageSum(): Attribute
    {
        return Attribute::make(
            get: static fn (int $value): float => $value / 1000,
            set: static fn (float $value): int => (int) ($value * 1000),
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<int, float> */
    protected function finalCummulativeGradePointAverage(): Attribute
    {
        return Attribute::make(
            get: static fn (int $value): float => $value / 1000,
            set: static fn (float $value): int => (int) ($value * 1000),
        );
    }
}
