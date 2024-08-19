<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\CourseRegistration;
use Exception;
use Illuminate\Support\Collection;

final class SaveResults
{
    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> $results
     * @throws \Exception
     */
    public function execute(Collection $results): void
    {
        $courseRegistrations = CourseRegistration::query()
            ->whereIn('online_id', $results->pluck('courseRegistrationId'))
            ->get();

        foreach ($results as $i => $result) {

            $courseRegistration = $courseRegistrations->where('online_id', $result->onlineId)->first();

            if (is_null($courseRegistration)) {
                throw new Exception(
                    'COURSE REGISTRATION NOT FOUND: Download course registration records and try again',
                );
            }

            clock()->event('Saving results')->name("saving-result-{$i}")->begin();
            $saveResultAction = new SaveResult();
            clock()->event("saving-result-{$i}")->end();

            $saveResultAction->execute($courseRegistration, $result);
        }
    }
}
