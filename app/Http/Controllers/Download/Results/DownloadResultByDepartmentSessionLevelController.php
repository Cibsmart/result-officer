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

final readonly class DownloadResultByDepartmentSessionLevelController
{
    public function __construct(private ResultService $service, private SaveResults $saveResult)
    {
    }

    /** @throws \Exception */
    public function __invoke(Request $request): RedirectResponse
    {
        $departmentId = $request->string('department.id')->value();
        $session = $request->string('session.name')->value();
        $level = $request->string('level.name')->value();

        $departmentOnlineId = Department::find($departmentId)->firstOrFail()->online_id;

        assert($departmentOnlineId !== null);

        try {
            $results = $this->service->getResultsByDepartmentSessionAndLevel(
                departmentId: $departmentOnlineId,
                session: $session,
                level: $level,
            );

            $responses = $this->saveResult->execute($results);

            $response = GetResponse::fromArray($responses);

            return back()->{$response->type->value}($response->message);
        } catch (Exception $e) {
            return back()->error($e->getMessage());
        }
    }
}
