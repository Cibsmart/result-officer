<?php

declare(strict_types=1);

namespace App\Http\Requests\Download;

use App\Models\Department;
use App\Models\Semester;
use App\Models\Session;
use App\Rules\AccessibleDepartment;
use Illuminate\Foundation\Http\FormRequest;

final class DownloadRegistrationsByDepartmentSessionSemesterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, list<\Illuminate\Contracts\Validation\ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'department' => ['required', 'array'],
            'department.id' => ['required', 'integer', 'exists:departments,id', new AccessibleDepartment()],
            'semester' => ['required', 'array'],
            'semester.id' => ['required', 'integer', 'exists:semesters,id'],
            'session' => ['required', 'array'],
            'session.id' => ['required', 'integer', 'exists:academic_sessions,id'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->replace([
            'departmentName' => $this->input('department.name'),

            'onlineDepartmentId' => Department::query()
                ->where('id', $this->input('department.id'))
                ->firstOrFail()
                ->online_id,

            'semesterName' => Semester::query()
                ->where('id', $this->input('semester.id'))
                ->firstOrFail()
                ->name,

            'sessionName' => Session::query()
                ->where('id', $this->input('session.id'))
                ->firstOrFail()
                ->name,
        ]);
    }
}
