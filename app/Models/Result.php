<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Models\ResultModelData;
use App\Data\Scores\ScoresData;
use App\Enums\RecordSource;
use App\Values\ExamScore;
use App\Values\InCourseScore;
use App\Values\RegistrationNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Result extends Model
{
    use SoftDeletes;

    public static function createFromRawResult(RawResult $rawResult, Registration $registration): self
    {
        return ResultModelData::fromRawResult($registration, $rawResult)->save();
    }

    public static function createFromLegacyResult(
        Registration $registration,
        LegacyResult|LegacyFinalResult $result,
    ): self {
        return ResultModelData::fromLegacyResult($registration, $result)->save();
    }

    /** @param array<string, int> $newScores */
    public static function updateResult(
        Student $student,
        Registration $registration,
        array $newScores,
    ): void {
        $result = $registration->result;

        if ($result === null) {
            $registrationNumber = RegistrationNumber::new($student->registration_number);

            $scores = ScoresData::fromArray($newScores);

            ResultModelData::fromResultUpdateInput(
                registration: $registration,
                registrationNumber: $registrationNumber,
                scores: $scores,
            )->save();

            return;
        }

        $result->scores = ScoresData::update($result->getScores(), $newScores)->value();
        $result->save();

        $result->recompute($student);
    }

    public static function createFromRawExcelResult(Registration $registration, RawExcelResult $rawResult): self
    {
        return ResultModelData::fromRawExcelResult($registration, $rawResult)->save();
    }

    public function recompute(Student $student): void
    {
        $total = ScoresData::fromArray($this->getScores())->total;

        $creditUnit = $this->registration->credit_unit;

        $grade = $total->grade($student->allowEGrade() || $this->registration->session()->allowsEGrade());
        $gradePoint = $grade->point() * $creditUnit->value;

        $this->update([
            'grade' => $grade->value,
            'grade_point' => $gradePoint,
            'total_score' => $total->value,
        ]);

        $this->resultDetail()->update(['value' => $this->getData()]);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\VettingReport, $this>
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\VettingReport, static>
     */
    public function vettingReports(): MorphMany
    {
        return $this->MorphMany(VettingReport::class, 'vettable');
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Registration, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Registration, static>
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\ResultDetail, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\ResultDetail, static>
     */
    public function resultDetail(): HasOne
    {
        return $this->hasOne(ResultDetail::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\RawResult, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\RawResult, static>
     */
    public function rawResult(): HasOne
    {
        return $this->hasOne(RawResult::class, 'result_id');
    }

    public function getData(): string
    {
        return "{$this->registration_id}-{$this->total_score}-{$this->grade}-{$this->grade_point}";
    }

    /** @return array{exam: int, in_course: int, in_course_2: int} */
    public function getScores(): array
    {
        $scores = $this->scores;

        if (is_string($scores)) {
            $scores = json_decode($scores);
            $inCourse2 = property_exists($scores, 'in_course_2')
                ? $scores->in_course_2
                : 0;

            return [
                'exam' => (int) $scores->exam,
                'in_course' => (int) $scores->in_course,
                'in_course_2' => $inCourse2,
            ];
        }

        $inCourse2 = array_key_exists('in_course_2', $scores)
            ? $scores['in_course_2']
            : 0;

        return [
            'exam' => (int) $scores['exam'],
            'in_course' => (int) $scores['in_course'],
            'in_course_2' => (int) $inCourse2,
        ];
    }

    /** @return array{exam: int, in_course: int, in_course_2: int} */
    public function prepareScores(
        ExamScore $examScore,
        InCourseScore $inCourseScore,
        InCourseScore $inCourseScore2,
    ): array {
        return [
            'exam' => $examScore->value,
            'in_course' => $inCourseScore->value,
            'in_course_2' => $inCourseScore2->value,
        ];
    }

    /** @return array{scores: 'json', source: 'App\Enums\RecordSource', upload_date: 'date'} */
    protected function casts(): array
    {
        return [
            'scores' => 'json',
            'source' => RecordSource::class,
            'upload_date' => 'date',
        ];
    }
}
