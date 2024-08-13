<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\CourseRegistrations;

use App\Helpers\GetResponse;
use App\Http\Requests\Download\DownloadRegistrationsByRegistrationNumberRequest;
use App\Repositories\CourseRegistrationRepository;
use Exception;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadRegistrationsByRegistrationNumberController
{
    public function __construct(private CourseRegistrationRepository $repository)
    {
    }

    public function __invoke(DownloadRegistrationsByRegistrationNumberRequest $request): RedirectResponse
    {
        try {
            $data = $this->repository->getCourseRegistrationsByRegistrationNumber(
                registrationNumber: $request->string('registrationNumber')->value(),
            );

            $results = $this->repository->saveCourseRegistrations($data);

            $response = GetResponse::fromArray($results);

            return back()->{$response->type->value}($response->message);
        } catch (Exception $e) {
            return back()->error($e->getMessage());
        }
    }
}
