<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Departments;

use App\Helpers\GetResponse;
use App\Repositories\DepartmentRepository;
use Exception;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadDepartmentsController
{
    public function __construct(private DepartmentRepository $repository)
    {
    }

    /** @throws \Exception */
    public function __invoke(): RedirectResponse
    {
        try {
            $departments = $this->repository->getDepartments();

            $saved = $this->repository->saveDepartments($departments);

            $response = GetResponse::fromArray($saved);

            return back()->{$response->type->value}($response->message);
        } catch (Exception $e) {
            return redirect()->back()->error($e->getMessage());
        }
    }
}
