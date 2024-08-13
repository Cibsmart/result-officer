<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\CourseRegistrations;

use App\Helpers\GetResponse;
use App\Http\Requests\Download\DownloadRegistrationsByDepartmentSessionSemesterRequest;
use App\Repositories\CourseRegistrationRepository;
use Exception;
use Illuminate\Http\RedirectResponse;

final class DownloadRegistrationsByDepartmentSessionSemesterController
{
    public function __construct(private CourseRegistrationRepository $repository)
    {
    }

    public function __invoke(DownloadRegistrationsByDepartmentSessionSemesterRequest $request): RedirectResponse
    {
        try {
            $data = $this->repository->getCourseRegistrationsByDepartmentSessionAndSemester(
                departmentId: $request->string('onlineDepartmentId')->value(),
                session: $request->string('sessionName')->value(),
                semester: $request->string('semesterName')->value(),
            );

            $results = $this->repository->saveCourseRegistrations($data);

            $response = GetResponse::fromArray($results);

            return back()->{$response->type->value}($response->message);
        } catch (Exception $e) {
            return back()->error($e->getMessage());
        }
    }
}
