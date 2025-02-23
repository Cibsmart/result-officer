<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Models\Student;
use App\Rules\Updated;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ProgramUpdateRequest extends FormRequest
{
    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return array<string, array<int, \App\Rules\Updated|\Illuminate\Validation\Rules\Unique|\Illuminate\Validation\Rules\Date|string>>
     */
    public function rules(): array
    {
        $student = $this->route('student');
        assert($student instanceof Student);

        return [
            'department' => ['required', 'integer', 'exists:departments,id'],
            'has_mail' => ['required', 'boolean'],
            'mail_date' => [
                'exclude_if:has_mail,false',
                'required',
                'regex:/^20\d{2}-\d{2}-\d{2}$/',
                Rule::date()->todayOrBefore(),
            ],
            'mail_title' => ['exclude_if:has_mail,false', 'required', 'string', 'min:10', 'max:255'],
            'program' => [
                'required',
                'integer',
                'exists:programs,id',
                new Updated($student->program_id),
            ],
            'remark' => ['required', 'string'],
        ];
    }
}
