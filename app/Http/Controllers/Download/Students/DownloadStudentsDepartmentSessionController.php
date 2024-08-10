<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Students;

use App\Enums\NotificationType;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Repositories\StudentRepository;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class DownloadStudentsDepartmentSessionController extends Controller
{
    public function __construct(public StudentRepository $repository)
    {
    }

    public function store(Request $request): RedirectResponse
    {
        $sessionName = $request->input('session')['name'];

        $departmentId = $request->input('department')['id'];

        $onlineDepartmentId = Department::findOrFail($departmentId)->online_id;

        try {
            $data = $this->repository->getStudentsByDepartmentAndSession($onlineDepartmentId, $sessionName);

            $results = $this->repository->saveStudents($data);

            [$notificationType, $message] = $this->getResponseMessage($results);

            return back()->$notificationType($message);
        } catch (Exception $e) {
            return back()->error($e->getMessage());
        }
    }

    /**
     * @param array<string, string|true> $results
     * @return array{string, string}
     */
    private function getResponseMessage(array $results): array
    {
        $totalNumber = count($results);
        $failed = array_filter($results, fn ($result) => $result !== true);
        $numberFailed = count($failed);
        $numberSuccessful = $totalNumber - $numberFailed;

        $successMessage = "$numberSuccessful records downloaded and saved successfully out of $totalNumber records";

        if ($numberFailed === 0) {
            return [NotificationType::SUCCESS->value, $successMessage];
        }

        $failureMessage = "$numberFailed failed to save with error messages: " . json_encode($failed);

        if ($numberFailed === $totalNumber) {
            return [NotificationType::ERROR->value, $failureMessage];
        }

        return [NotificationType::INFO->value, "SUCCESS: $successMessage. FAIL: $failureMessage"];
    }
}
