<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ingest;

use App\Http\Controllers\Controller;
use App\Http\Requests\Students\DownloadRequest;
use App\Repositories\StudentRepository;
use Exception;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class StudentRecordController extends Controller
{
    public function __construct(private readonly StudentRepository $repository)
    {
    }

    public function form(): Response
    {
        return Inertia::render('students/download/form/page');
    }

    public function view(DownloadRequest $request): RedirectResponse
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
