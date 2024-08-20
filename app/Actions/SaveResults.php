<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\Response\ResponseData;
use App\Models\CourseRegistration;
use Exception;
use Illuminate\Support\Collection;

final class SaveResults
{
    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> $results
     * @return \Illuminate\Support\Collection<int, \App\Data\Response\ResponseData>
     * @throws \Exception
     */
    public function execute(Collection $results): Collection
    {
        $courseRegistrations = CourseRegistration::query()
            ->whereIn('online_id', $results->pluck('courseRegistrationId'))
            ->get();

        $saveResultAction = new SaveResult();

        $responses = [];

        foreach ($results as $result) {

            $courseRegistration = $courseRegistrations->where('online_id', $result->onlineId)->first();

            try {
                $saveResultAction->execute($courseRegistration, $result);

                $responses[] = ResponseData::from([$result->registrationNumber, true]);
            } catch (Exception $e) {
                $responses[] = ResponseData::from([$result->registrationNumber, $e->getMessage()]);

                continue;
            }
        }

        return ResponseData::collect(collect($responses));
    }
}
