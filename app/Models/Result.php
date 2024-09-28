<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RecordSource;
use App\Values\DateValue;
use App\Values\RegistrationNumber;
use App\Values\TotalScore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Result extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'semester_enrollment_id',
        'scores',
        'total_score',
        'grade',
        'grade_point',
        'remarks',
        'data',
    ];

    /** @var array<int, string> */
    protected $hidden = ['data'];

    public static function createFromRawResult(RawResult $rawResult, Registration $registration): void
    {
        $registrationNumber = RegistrationNumber::new($rawResult->registration_number);
        $totalScore = TotalScore::new((int) $rawResult->in_course + (int) $rawResult->exam);
        $grade = $totalScore->grade($registrationNumber->allowEGrade());
        $gradePoint = $grade->point() * $registration->credit_unit;

        $scores = [
            'exam' => $rawResult->exam,
            'grade' => $rawResult->grade,
            'in-course' => $rawResult->in_course,
            'total' => $rawResult->total,
        ];

        $result = new self();
        $result->registration_id = $registration->id;
        $result->scores = $scores;
        $result->total_score = $totalScore->value;
        $result->grade = $grade->value;
        $result->grade_point = $gradePoint;
        $result->upload_date = DateValue::fromString($rawResult->upload_date)->value;
        $result->data = $result->getData();
        $result->remarks = null;
        $result->source = RecordSource::PORTAL;

        $result->lecturer_name = $rawResult->lecturer_name;
        $result->lecturer_phone = $rawResult->lecturer_phone;
        $result->lecturer_email = $rawResult->lecturer_email;
        $result->lecturer_department = $rawResult->lecturer_department;

        $result->save();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Registration, \App\Models\Result> */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function getData(): string
    {
        return "{$this->registration_id}-{$this->total_score}-{$this->grade}-{$this->grade_point}";
    }

    /** @return array{data: 'hashed', scores: 'array', source: 'App\Enums\RecordSource', upload_date: 'date'} */
    protected function casts(): array
    {
        return [
            'data' => 'hashed',
            'scores' => 'array',
            'source' => RecordSource::class,
            'upload_date' => 'date',
        ];
    }
}
