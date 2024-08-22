<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Courses;

use App\Helpers\GetResponse;
use App\Repositories\CourseRepository;
use Exception;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadCoursesController
{
    public function __construct(private CourseRepository $repository)
    {
    }

    /** @throws \Exception */
    public function __invoke(): RedirectResponse
    {
        try {
            $courses = $this->repository->getCourses();

            $saved = $this->repository->saveCourses($courses);

            $response = GetResponse::fromArray($saved);

            return back()->{$response->type->value}($response->message);
        } catch (Exception $e) {
            return redirect()->back()->error($e->getMessage());
        }
    }
}
