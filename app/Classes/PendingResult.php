<?php

declare(strict_types=1);

namespace App\Classes;

use App\Data\Download\PortalResultData;
use App\Models\CourseRegistration;
use App\Models\Result;
use App\Values\DateValue;
use App\Values\RegistrationNumber;
use App\Values\TotalScore;
use Exception;

final readonly class PendingResult
{
    public function __construct(public CourseRegistration $courseRegistration, public Result $result)
    {
    }

    public static function new(CourseRegistration $courseRegistration, PortalResultData $resultData): self
    {
        $registrationNumber = RegistrationNumber::new($resultData->registrationNumber);
        $totalScore = TotalScore::new((int) $resultData->inCourseScore + (int) $resultData->examScore);
        $grade = $totalScore->grade($registrationNumber->allowEGrade());

        $scores = [
            'exam' => $resultData->examScore,
            'in-course' => $resultData->inCourseScore,
            'total' => $resultData->totalScore,
        ];
        $gradePoint = $grade->point() * $courseRegistration->credit_unit;

        $result = new Result();

        $result->course_registration_id = $courseRegistration->id;
        $result->scores = $scores;
        $result->total_score = $totalScore->value;
        $result->grade = $grade->value;
        $result->grade_point = $gradePoint;
        $result->upload_date = DateValue::fromString($resultData->uploadDate)->value;
        $result->data = $result->getData();
        $result->remarks = null;
        $result->source = $resultData->source;

        return new self($courseRegistration, $result);
    }

    /** @throws \Exception */
    public function save(): bool
    {
        $result = $this->courseRegistration->result;

        if (! is_null($result)) {
            throw new Exception('RESULT ALREADY EXISTS: Not Saved');
        }

        return $this->result->save();
    }
}
