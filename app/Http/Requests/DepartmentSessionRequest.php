<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\AccessibleDepartment;
use Illuminate\Foundation\Http\FormRequest;

final class DepartmentSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<\Illuminate\Contracts\Validation\ValidationRule|string>> */
    public function rules(): array
    {
        return [
            'department' => ['required', 'array'],
            'department.id' => ['required', 'integer', 'exists:departments,id', new AccessibleDepartment()],
            'session' => ['required', 'array'],
            'session.id' => ['required', 'integer', 'exists:academic_sessions,id'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->replace([
            'departmentId' => $this->input('department.id'),
            'departmentName' => $this->input('department.name'),

            'sessionId' => $this->input('session.id'),
            'sessionName' => $this->input('session.name'),
        ]);
    }
}
