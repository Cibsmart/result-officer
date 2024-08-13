<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\CourseRegistrations;

use App\Helpers\GetResponse;
use App\Http\Requests\Download\DownloadRegistrationsBySessionCourseRequest;
use App\Repositories\CourseRegistrationRepository;
use Exception;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadRegistrationsBySessionCourseController
{
    public function __construct(private CourseRegistrationRepository $repository)
    {
    }

    public function __invoke(DownloadRegistrationsBySessionCourseRequest $request): RedirectResponse
    {
        try {
            $data = $this->repository->getCourseRegistrationsBySessionAndCourse(
                session: $request->string('sessionName')->value(),
                courseId: $request->string('onlineCourseId')->value(),
            );

            $results = $this->repository->saveCourseRegistrations($data);

            $response = GetResponse::fromArray($results);

            return back()->{$response->type->value}($response->message);
        } catch (Exception $e) {
            return back()->error($e->getMessage());
        }
    }
}
