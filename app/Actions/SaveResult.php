<?php

declare(strict_types=1);

namespace App\Actions;

use App\Classes\PendingResult;
use App\Data\Download\PortalResultData;
use App\Models\CourseRegistration;
use Exception;

final class SaveResult
{
    /** @throws \Exception */
    public function execute(PortalResultData $data): bool
    {
        $courseRegistration = CourseRegistration::find($data->courseRegistrationId);

        if (is_null($courseRegistration)) {
            throw new Exception('COURSE REGISTRATION NOT FOUND: Download course registration records and try again');
        }

        $pendingResult = PendingResult::new(courseRegistration: $courseRegistration, resultData: $data);

        return $pendingResult->save($courseRegistration);
    }
}
