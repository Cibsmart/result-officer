<?php

declare(strict_types=1);

namespace App\Http\Requests\Download;

use App\Models\Department;
use App\Models\Level;
use App\Models\Session;
use App\Rules\AccessibleDepartment;
use Illuminate\Foundation\Http\FormRequest;

final class DownloadRegistrationsByDepartmentSessionLevelRequest extends FormRequest
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
            'level' => ['required', 'array'],
            'level.id' => ['required', 'integer', 'exists:levels,id'],
            'session' => ['required', 'array'],
            'session.id' => ['required', 'integer', 'exists:academic_sessions,id'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->replace([
            'departmentName' => $this->input('department.name'),

            'levelName' => Level::query()
                ->where('id', $this->input('level.id'))
                ->firstOrFail()
                ->name,

            'onlineDepartmentId' => Department::query()
                ->where('id', $this->input('department.id'))
                ->firstOrFail()
                ->online_id,

            'sessionName' => Session::query()
                ->where('id', $this->input('session.id'))
                ->firstOrFail()
                ->name,
        ]);
    }
}
