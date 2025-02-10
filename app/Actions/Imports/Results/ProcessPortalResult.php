<?php

declare(strict_types=1);

namespace App\Actions\Import\Results;

use App\Enums\RawDataStatus;
use App\Models\RawResult;
use App\Models\Registration;
use App\Models\Result;

final class ProcessPortalResult
{
    /** @throws \Exception */
    public function execute(RawResult $rawResult): void
    {
        $registration = Registration::getUsingOnlineId((string) $rawResult->registration_id);

        $exists = Result::query()
            ->where('registration_id', $registration->id)
            ->exists();

        if ($exists) {
            $rawResult->updateStatus(RawDataStatus::DUPLICATE);

            return;
        }

        $result = Result::createFromRawResult($rawResult, $registration);

        $rawResult->updateStatusAndResult(RawDataStatus::PROCESSED, $result);
    }
}
