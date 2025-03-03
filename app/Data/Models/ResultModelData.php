<?php

declare(strict_types=1);

namespace App\Data\Models;

use App\Data\Scores\ScoresData;
use App\Enums\CreditUnit;
use App\Enums\Grade;
use App\Enums\RecordSource;
use App\Models\Lecturer;
use App\Models\LegacyFinalResult;
use App\Models\LegacyResult;
use App\Models\RawExcelResult;
use App\Models\RawResult;
use App\Models\Registration;
use App\Models\Result;
use App\Values\DateValue;
use App\Values\RegistrationNumber;
use App\Values\TotalScore;

final readonly class ResultModelData
{
    public function __construct(
        public Registration $registration,
        /** @var array<string, int> $scores */
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
        $scores = ScoresData::new((int) $result->exam, (int) $result->in_course, (int) $result->in_course_2);

        [$grade, $gradePoint] = self::getGrade(
            registration: $registration,
            registrationNumber: RegistrationNumber::new($result->getRegistrationNumber()),
            scores: $scores,
        );

        $lecturer = null;

        if (! is_null($result->lecturer_name)) {
            $lecturer = Lecturer::getOrCreateFromRawResult($result);
        }

        return new self(
            registration: $registration,
            scores: $scores->value(),
            totalScore: $scores->total,
            grade: $grade,
            gradePoint: $gradePoint,
            examDate: DateValue::fromValue($result->exam_date),
            uploadDate: DateValue::fromValue($result->upload_date),
            lecturer: $lecturer,
            remarks: null,
            source: RecordSource::PORTAL,
        );
    }

    public static function fromRawExcelResult(Registration $registration, RawExcelResult $result): self
    {
        $scores = ScoresData::new((int) $result->exam, (int) $result->in_course, (int) $result->in_course_2);

        [$grade, $gradePoint] = self::getGrade(
            registration: $registration,
            registrationNumber: RegistrationNumber::new($result->registration_number),
            scores: $scores,
        );

        $lecturer = null;

        if (! is_null($result->examiner)) {
            $lecturer = Lecturer::getOrCreateUsingName($result->examiner);
        }

        return new self(
            registration: $registration,
            scores: $scores->value(),
            totalScore: $scores->total,
            grade: $grade,
            gradePoint: $gradePoint,
            examDate: DateValue::fromValue($result->exam_date),
            uploadDate: DateValue::fromValue(null),
            lecturer: $lecturer,
            remarks: null,
            source: RecordSource::EXCEL,
        );
    }

    public static function fromLegacyResult(Registration $registration, LegacyResult|LegacyFinalResult $result): self
    {
        $scores = ScoresData::new((int) $result->exam, (int) $result->inc, 0);

        [$grade, $gradePoint] = self::getGrade(
            registration: $registration,
            registrationNumber: RegistrationNumber::new($result->registration_number),
            scores: $scores,
        );

        $lecturer = null;

        if (! is_null($result->examiner)) {
            $lecturer = Lecturer::getOrCreateUsingName($result->examiner);
        }

        return new self(
            registration: $registration,
            scores: $scores->value(),
            totalScore: $scores->total,
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
        ScoresData $scores,
    ): self {

        [$grade, $gradePoint] = self::getGrade(
            registration: $registration,
            registrationNumber: $registrationNumber,
            scores: $scores,
        );

        return new self(
            registration: $registration,
            scores: $scores->value(),
            totalScore: $scores->total,
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

    /** @return array{\App\Enums\Grade, int} */
    private static function getGrade(
        Registration $registration,
        RegistrationNumber $registrationNumber,
        ScoresData $scores,
    ): array {
        $creditUnit = $registration->credit_unit;
        assert($creditUnit instanceof CreditUnit);

        $totalScore = $scores->total;
        $grade = $totalScore->grade($registrationNumber->allowEGrade() || $registration->session()->allowsEGrade());
        $gradePoint = $grade->point() * $creditUnit->value;

        return [$grade, $gradePoint];
    }
}
