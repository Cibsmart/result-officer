<?php

declare(strict_types=1);

namespace App\Data\Models;

use App\Enums\CreditUnit;
use App\Enums\Grade;
use App\Enums\RecordSource;
use App\Models\Lecturer;
use App\Models\LegacyFinalResult;
use App\Models\LegacyResult;
use App\Models\RawResult;
use App\Models\Registration;
use App\Models\Result;
use App\Values\DateValue;
use App\Values\ExamScore;
use App\Values\InCourseScore;
use App\Values\RegistrationNumber;
use App\Values\TotalScore;

final readonly class ResultModelData
{
    public function __construct(
        public Registration $registration,
        /** @var array<string, string> $scores */
        public array $scores,
        public TotalScore $totalScore,
        public Grade $grade,
        public int $gradePoint,
        public DateValue $examDate,
        public DateValue $uploadDate,
        public ?Lecturer $lecturer,
        public ?string $remarks,
        public RecordSource $source,
    ) {
    }

    public static function fromRawResult(Registration $registration, RawResult $result): self
    {
        [$totalScore, $grade, $gradePoint] = self::getTotalAndGrade(
            registration: $registration,
            registrationNumber: RegistrationNumber::new($result->getRegistrationNumber()),
            exam: ExamScore::new((int) $result->exam),
            inCourse: InCourseScore::new((int) $result->in_course),
        );

        $lecturer = null;

        if (! is_null($result->lecturer_name)) {
            $lecturer = Lecturer::getOrCreateFromRawResult($result);
        }

        return new self(
            registration: $registration,
            scores: ['exam' => $result->exam, 'in_course' => $result->in_course],
            totalScore: $totalScore,
            grade: $grade,
            gradePoint: $gradePoint,
            examDate: DateValue::fromValue($result->exam_date),
            uploadDate: DateValue::fromValue($result->upload_date),
            lecturer: $lecturer,
            remarks: null,
            source: RecordSource::PORTAL,
        );
    }

    public static function fromLegacyResult(Registration $registration, LegacyResult|LegacyFinalResult $result): self
    {
        [$totalScore, $grade, $gradePoint] = self::getTotalAndGrade(
            registration: $registration,
            registrationNumber: RegistrationNumber::new($result->registration_number),
            exam: ExamScore::new($result->exam),
            inCourse: InCourseScore::new($result->inc),
        );

        $lecturer = null;

        if (! is_null($result->examiner)) {
            $lecturer = Lecturer::getOrCreateUsingName($result->examiner);
        }

        return new self(
            registration: $registration,
            scores: ['exam' => (string) $result->exam, 'in_course' => (string) $result->inc],
            totalScore: $totalScore,
            grade: $grade,
            gradePoint: $gradePoint,
            examDate: DateValue::fromValue($result->exam_date),
            uploadDate: DateValue::fromValue(null),
            lecturer: $lecturer,
            remarks: null,
            source: RecordSource::LEGACY,
        );
    }

    public static function fromResultUpdateInput(
        Registration $registration,
        RegistrationNumber $registrationNumber,
        ExamScore $exam,
        InCourseScore $inCourse,
    ): self {
        [$totalScore, $grade, $gradePoint] = self::getTotalAndGrade(
            registration: $registration,
            registrationNumber: $registrationNumber,
            exam: $exam, inCourse: $inCourse,
        );

        return new self(
            registration: $registration,
            scores: ['exam' => (string) $exam->value, 'in_course' => (string) $inCourse->value],
            totalScore: $totalScore,
            grade: $grade,
            gradePoint: $gradePoint,
            examDate: DateValue::fromValue(null),
            uploadDate: DateValue::fromValue(null),
            lecturer: null,
            remarks: null,
            source: RecordSource::USER,
        );
    }

    public function getModel(): Result
    {
        $result = new Result();

        $result->registration_id = $this->registration->id;
        $result->scores = $this->scores;
        $result->total_score = $this->totalScore->value;
        $result->grade = $this->grade->value;
        $result->grade_point = $this->gradePoint;
        $result->exam_date = $this->examDate->value;
        $result->upload_date = $this->uploadDate->value;
        $result->remarks = $this->remarks;
        $result->source = $this->source;
        $result->lecturer_id = $this->lecturer
            ? $this->lecturer->id
            : null;

        return $result;
    }

    public function save(): Result
    {
        $result = $this->getModel();

        $result->save();

        $result->resultDetail()->create(['value' => $result->getData()]);

        return $result;
    }

    /** @return array{\App\Values\TotalScore, \App\Enums\Grade, int} */
    private static function getTotalAndGrade(
        Registration $registration,
        RegistrationNumber $registrationNumber,
        ExamScore $exam,
        InCourseScore $inCourse,
    ): array {
        $creditUnit = $registration->credit_unit;
        assert($creditUnit instanceof CreditUnit);

        $totalScore = TotalScore::fromInCourseAndExam($inCourse, $exam);
        $grade = $totalScore->grade($registrationNumber->allowEGrade() || $registration->session()->allowsEGrade());
        $gradePoint = $grade->point() * $creditUnit->value;

        return [$totalScore, $grade, $gradePoint];
    }
}
