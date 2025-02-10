<?php

declare(strict_types=1);

namespace App\Http\Requests\Imports;

use Illuminate\Foundation\Http\FormRequest;

final class CurriculumImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|string|array<int, string>> */
    public function rules(): array
    {
        return [
            'department' => ['required'],
            'department.id' => ['required', 'integer', 'exists:departments,id'],
            'file' => ['required', 'file', 'mimes:xlsx'],
            'program' => ['required'],
            'program.id' => ['required', 'integer', 'exists:programs,id'],
        ];
    }
}
