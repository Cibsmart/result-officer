<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Results;

use App\Actions\SaveResults;
use App\Helpers\GetResponse;
use App\Models\Department;
use App\Services\Api\ResultService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class DownloadResultByDepartmentSessionSemesterController
{
    public function __construct(private ResultService $service, private SaveResults $saveResult)
    {
    }

    /** @throws \Exception */
    public function __invoke(Request $request): RedirectResponse
    {
        $departmentId = $request->string('department.id')->value();
        $session = $request->string('session.name')->value();
        $semester = $request->string('semester.name')->value();

        $departmentOnlineId = Department::find($departmentId)->firstOrFail()->online_id;

        assert($departmentOnlineId !== null);

        try {
            $results = $this->service->getResultsByDepartmentSessionAndSemester(
                departmentId: $departmentOnlineId,
                session: $session,
                semester: $semester,
            );

            $responses = $this->saveResult->execute($results);

            $response = GetResponse::fromArray($responses);

            return back()->{$response->type->value}($response->message);
        } catch (Exception $e) {
            return back()->error($e->getMessage());
        }
    }
}
