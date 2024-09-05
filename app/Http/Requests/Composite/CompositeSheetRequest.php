<?php

declare(strict_types=1);

namespace App\Http\Requests\Composite;

use App\Models\Level;
use App\Models\Program;
use App\Models\Semester;
use App\Models\Session;
use Illuminate\Foundation\Http\FormRequest;

final class CompositeSheetRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<string>>
     */
    public function rules(): array
    {
        return [
            'department' => ['required', 'array'],
            'department.id' => ['required', 'integer'],
            'level' => ['required', 'array'],
            'level.id' => ['required', 'integer', 'exists:levels,id'],
            'semester' => ['required', 'array'],
            'semester.id' => ['required', 'integer', 'exists:semesters,id'],
            'session' => ['required', 'array'],
            'session.id' => ['required', 'integer', 'exists:academic_sessions,id'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->replace([
            'level' => Level::query()->findOrFail($this->input('level.id')),
            'program' => Program::query()->findOrFail($this->input('department.id')),
            'semester' => Semester::query()->findOrFail($this->input('semester.id')),
            'session' => Session::query()->findOrFail($this->input('session.id')),
        ]);
    }
}
