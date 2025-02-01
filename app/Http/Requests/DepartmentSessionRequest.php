<?php

namespace App\Http\Requests;

use App\Models\Department;
use App\Models\Session;
use Illuminate\Foundation\Http\FormRequest;

class DepartmentSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'department' => ['required', 'array'],
            'department.id' => ['required', 'integer', 'exists:departments,id'],
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
