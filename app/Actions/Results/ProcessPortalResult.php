<?php

declare(strict_types=1);

namespace App\Actions\Results;

use App\Enums\RawDataStatus;
use App\Models\CourseRegistration;
use App\Models\RawResult;
use App\Models\Result;

final class ProcessPortalResult
{
    /** @throws \Exception */
    public function execute(RawResult $rawResult): void
    {
        $registration = CourseRegistration::getUsingOnlineId($rawResult->course_registration_id);

        $exists = Result::query()
            ->where('course_registration_id', $registration->id)
            ->exists();

        if ($exists) {
            $rawResult->updateStatus(RawDataStatus::DUPLICATE);

            return;
        }

        Result::createFromRawResult($rawResult, $registration);

        $rawResult->updateStatus(RawDataStatus::PROCESSED);
    }
}
