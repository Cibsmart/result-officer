<?php

declare(strict_types=1);

namespace App\Http\Requests\Imports;

use App\Rules\AccessibleDepartment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

final class CurriculumImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<object|string>|object> */
    public function rules(): array
    {
        return [
            'department' => ['required'],
            'department.id' => ['required', 'integer', 'exists:departments,id', new AccessibleDepartment()],
            'file' => File::types(['xlsx'])->max('8mb'),
            'program' => ['required'],
            'program.id' => ['required', 'integer', 'exists:programs,id'],
        ];
    }
}
