<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Students;

use App\Helpers\GetResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Download\DownloadStudentsByDepartmentSessionRequest;
use App\Repositories\StudentRepository;
use Exception;
use Illuminate\Http\RedirectResponse;

final class DownloadStudentsByDepartmentSessionController extends Controller
{
    public function __construct(public StudentRepository $repository)
    {
    }

    public function store(DownloadStudentsByDepartmentSessionRequest $request): RedirectResponse
    {
        try {
            $data = $this->repository->getStudentsByDepartmentAndSession(
                departmentId: $request->string('onlineDepartmentId')->value(),
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
