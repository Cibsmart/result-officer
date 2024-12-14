<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RecordSource;
use App\Values\DateValue;
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

    /** @var array<int, string> */
    protected $fillable = [
        'semester_enrollment_id',
        'scores',
        'total_score',
        'grade',
        'grade_point',
        'remarks',
    ];

    public static function createFromRawResult(RawResult $rawResult, Registration $registration): self
    {
        $registrationNumber = RegistrationNumber::new($rawResult->registration_number);
        $totalScore = TotalScore::new((int) $rawResult->in_course + (int) $rawResult->exam);
        $grade = $totalScore->grade($registrationNumber->allowEGrade());
        $gradePoint = $grade->point() * $registration->credit_unit->value;

        $lecturer = null;

        if (! is_null($rawResult->lecturer_name)) {
            $lecturer = Lecturer::getOrCreateFromRawResult($rawResult)->id;
        }

        $scores = [
            'exam' => $rawResult->exam,
            'in_course' => $rawResult->in_course,
        ];

        $result = new self();
        $result->registration_id = $registration->id;
        $result->scores = $scores;
        $result->total_score = $totalScore->value;
        $result->grade = $grade->value;
        $result->grade_point = $gradePoint;
        $result->upload_date = DateValue::fromValue($rawResult->upload_date)->value;
        $result->remarks = null;
        $result->source = RecordSource::PORTAL;
        $result->lecturer_id = $lecturer;

        $result->save();

        $result->resultDetail()->create(['value' => $result->getData()]);

        return $result;
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
