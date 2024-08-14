<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Students;

use App\Http\Requests\Download\DownloadStudentByRegistrationRequest;
use App\Repositories\StudentRepository;
use Exception;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadStudentByRegistrationNumberController
{
    public function __construct(private StudentRepository $repository)
    {
    }

    public function __invoke(DownloadStudentByRegistrationRequest $request): RedirectResponse
    {
        try {
            $data = $this->repository->getStudentByRegistrationNumber(
                $request->string('registration_number')->value(),
            );

            $result = $this->repository->saveStudent($data);

            return back()->success("$result->registration_number Record Downloaded and Saved Successfully");
        } catch (Exception $e) {
            return back()->error($e->getMessage());
        }
    }
}
