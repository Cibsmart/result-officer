<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Students\Updates\StudentStatusUpdate;
use App\Models\Student;
use App\Rules\Updated;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StudentStatusUpdateRequest extends FormRequest
{
    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return array<string, array<int, \App\Rules\Updated|\Illuminate\Validation\Rules\Enum|\Illuminate\Validation\Rules\Date|string>>
     */
    public function rules(): array
    {
        $student = $this->route('student');
        assert($student instanceof Student);

        return [
            'has_mail' => ['required', 'boolean'],
            'mail_date' => [
                'exclude_if:has_mail,false',
                'required',
                'regex:/^20\d{2}-\d{2}-\d{2}$/',
                Rule::date()->todayOrBefore(),
            ],
            'mail_title' => ['exclude_if:has_mail,false', 'required', 'string', 'min:10', 'max:255'],
            'remark' => ['required', 'string'],
            'status' => [
                'required',
                'string',
                Rule::enum(StudentStatusUpdate::class),
                new Updated($student->status->value),
            ],
        ];
    }
}
