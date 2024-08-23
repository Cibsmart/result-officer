<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Results;

use App\Helpers\GetResponse;
use App\Models\Course;
use App\Repositories\ResultRepository;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class DownloadResultBySessionCourseController
{
    public function __construct(private ResultRepository $repository)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $session = $request->string('session.name')->value();
        $courseId = $request->string('course.id')->value();

        $courseOnlineId = Course::find($courseId)->firstOrFail()->online_id;

        assert($courseOnlineId !== null);

        try {
            $results = $this->repository->getResultBySessionAndCourse(session: $session, course: $courseOnlineId);

            $responses = $this->repository->saveResults($results);

            $response = GetResponse::fromArray($responses);

            return back()->{$response->type->value}($response->message);
        } catch (Exception $e) {
            return back()->error($e->getMessage());
        }
    }
}
