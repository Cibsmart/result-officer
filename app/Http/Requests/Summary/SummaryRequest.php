<?php

declare(strict_types=1);

namespace App\Http\Requests\Summary;

use App\Models\Department;
use App\Models\Level;
use App\Models\Session;
use Illuminate\Foundation\Http\FormRequest;

final class SummaryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<string>>
     */
    public function rules(): array
    {
        return [
            'department' => ['required', 'array'],
            'department.id' => ['required', 'integer', 'exists:departments,id'],
            'level' => ['required', 'array'],
            'level.id' => ['required', 'integer', 'exists:levels,id'],
            'session' => ['required', 'array'],
            'session.id' => ['required', 'integer', 'exists:academic_sessions,id'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->replace([
            'department' => Department::query()->findOrFail($this->input('department.id')),
            'level' => Level::query()->findOrFail($this->input('level.id')),
            'session' => Session::query()->findOrFail($this->input('session.id')),
        ]);
    }
}
