<?php

declare(strict_types=1);

namespace App\Classes;

use App\Data\Download\PortalResultData;
use App\Models\CourseRegistration;
use App\Models\Result;
use App\Values\RegistrationNumber;
use App\Values\TotalScore;

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

        $scores = self::prepareScores($resultData->inCourseScore, $resultData->examScore, $resultData->totalScore);
        $gradePoint = $grade->point() * $courseRegistration->credit_unit;

        $result = new Result();

        $result->course_registration_id = $courseRegistration->id;
        $result->scores = $scores;
        $result->total_score = $totalScore->value;
        $result->grade = $grade->value;
        $result->grade_point = $gradePoint;
        $result->upload_date = $resultData->uploadDate->getStringDate();
        $result->data = $result->getData();
        $result->remarks = null;
        $result->source = $resultData->source->value;

        return new self($courseRegistration, $result);
    }

    public function save(): bool
    {
        $result = $this->courseRegistration->result;

        if (! is_null($result)) {
            return false;
        }

        return $this->result->save();
    }

    private static function prepareScores(
        string $inCourse,
        string $exam,
        string $total,
    ): string {
        $scores = json_encode(['in-course' => $inCourse, 'exam' => $exam, 'total' => $total]);
        assert(is_string($scores));

        return $scores;
    }
}
