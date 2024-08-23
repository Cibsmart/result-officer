<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Results;

use App\Helpers\GetResponse;
use App\Models\Department;
use App\Repositories\ResultRepository;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class DownloadResultByDepartmentSessionLevelController
{
    public function __construct(private ResultRepository $repository)
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
            $results = $this->repository->getResultByDepartmentSessionAndLevel(
                departmentId: $departmentOnlineId,
                session: $session,
                level: $level,
            );

            $responses = $this->repository->saveResults($results);

            $response = GetResponse::fromArray($responses);

            return back()->{$response->type->value}($response->message);
        } catch (Exception $e) {
            return back()->error($e->getMessage());
        }
    }
}
