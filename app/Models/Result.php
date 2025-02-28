<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Models\ResultModelData;
use App\Enums\RecordSource;
use App\Values\ExamScore;
use App\Values\InCourseScore;
use App\Values\RegistrationNumber;
use App\Values\TotalScore;
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
            $inCourse = InCourseScore::new($newScores['in_course'] ?? 0);
            $exam = ExamScore::new($newScores['exam'] ?? 0);

            ResultModelData::fromResultUpdateInput($registration, $registrationNumber, $exam, $inCourse)->save();

            return;
        }

        $oldScores = $result->getScores();
        $scores = ['in_course' => $oldScores['in_course'], 'exam' => $oldScores['exam']];

        foreach ($newScores as $key => $value) {
            $scores[$key] = $value;
        }

        $result->scores = $scores;
        $result->save();

        $result->recompute($student);
    }

    public function recompute(Student $student): void
    {
        $scores = $this->getScores();

        $creditUnit = $this->registration->credit_unit;
        $inCourse = $scores['in_course'];
        $exam = $scores['exam'];

        $total = TotalScore::new($inCourse + $exam);
        $grade = $total->grade($student->allowEGrade() || $this->registration->session()->allowsEGrade());
        $gradePoint = $grade->point() * $creditUnit->value;

        $this->update([
            'grade' => $grade->value,
            'grade_point' => $gradePoint,
            'total_score' => $total->value,
        ]);

        $this->resultDetail()->update(['value' => $this->getData()]);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\VettingReport, \App\Models\Result> */
    public function vettingReports(): MorphMany
    {
        return $this->MorphMany(VettingReport::class, 'vettable');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Registration, \App\Models\Result> */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\ResultDetail, \App\Models\Result> */
    public function resultDetail(): HasOne
    {
        return $this->hasOne(ResultDetail::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\RawResult, \App\Models\Result> */
    public function rawResult(): HasOne
    {
        return $this->hasOne(RawResult::class, 'result_id');
    }

    public function getData(): string
    {
        return "{$this->registration_id}-{$this->total_score}-{$this->grade}-{$this->grade_point}";
    }

    /** @return array{in_course: int, exam: int} */
    public function getScores(): array
    {
        $scores = $this->scores;

        if (is_string($scores)) {
            $scores = json_decode($scores);

            return ['in_course' => (int) $scores->in_course, 'exam' => (int) $scores->exam];
        }

        return ['in_course' => (int) $scores['in_course'], 'exam' => (int) $scores['exam']];
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
