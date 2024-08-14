<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Students;

use App\Helpers\GetResponse;
use App\Http\Requests\Download\DownloadStudentsBySessionRequest;
use App\Repositories\StudentRepository;
use Exception;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadStudentsBySessionController
{
    public function __construct(private StudentRepository $repository)
    {
    }

    public function __invoke(DownloadStudentsBySessionRequest $request): RedirectResponse
    {
        try {
            $data = $this->repository->getStudentsBySession(
                session: $request->string('sessionName')->value(),
            );

            $results = $this->repository->saveStudents($data);

            $response = GetResponse::fromArray($results);

            return back()->{$response->type->value}($response->message);
        } catch (Exception $e) {
            return back()->error($e->getMessage());
        }
    }
}
