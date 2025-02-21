<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Models\ResultModelData;
use App\Enums\RecordSource;
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
        $result = ResultModelData::fromRawResult($registration, $rawResult)->getModel();

        $result->save();

        $result->resultDetail()->create(['value' => $result->getData()]);

        return $result;
    }

    public static function createFromLegacyResult(
        Registration $registration,
        LegacyResult|LegacyFinalResult $result,
    ): self {
        $result = ResultModelData::fromLegacyResult($registration, $result)->getModel();

        $result->save();

        $result->resultDetail()->create(['value' => $result->getData()]);

        return $result;
    }

    /** @param array<string, int> $newScores */
    public static function updateResult(self $result, array $newScores): void
    {
        $oldScores = json_decode($result->scores);
        $scores = ['in_course' => $oldScores->in_course, 'exam' => $oldScores->exam];

        foreach ($newScores as $key => $value) {
            $scores[$key] = $value;
        }

        $result->scores = $scores;
        $result->save();
    }

    public function recompute(Student $student): void
    {
        $scores = $this->scores;

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

    public function getData(): string
    {
        return "{$this->registration_id}-{$this->total_score}-{$this->grade}-{$this->grade_point}";
    }

    /** @return array{scores: 'array', source: 'App\Enums\RecordSource', upload_date: 'date'} */
    protected function casts(): array
    {
        return [
            'scores' => 'json',
            'source' => RecordSource::class,
            'upload_date' => 'date',
        ];
    }
}
