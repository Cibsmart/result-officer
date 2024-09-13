<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\CourseRegistrations;

use App\Helpers\GetResponse;
use App\Http\Requests\Download\DownloadRegistrationsByDepartmentSessionLevelRequest;
use App\Repositories\CourseRegistrationRepository;
use Exception;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadRegistrationsByDepartmentSessionLevelController
{
    public function __construct(private CourseRegistrationRepository $repository)
    {
    }

    public function __invoke(DownloadRegistrationsByDepartmentSessionLevelRequest $request): RedirectResponse
    {
        try {
            $data = $this->repository->getCourseRegistrationsByDepartmentAndSessionLevel(
                departmentId: $request->integer('onlineDepartmentId'),
                session: $request->string('sessionName')->value(),
                level: $request->integer('levelName'),
            );

            $results = $this->repository->saveCourseRegistrations($data);

            $response = GetResponse::fromArray($results);

            return back()->{$response->type->value}($response->message);
        } catch (Exception $e) {
            return back()->error($e->getMessage());
        }
    }
}
