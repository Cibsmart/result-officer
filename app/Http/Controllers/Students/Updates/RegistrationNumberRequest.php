<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Models\Student;
use App\Rules\Updated;
use App\Values\RegistrationNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class RegistrationNumberRequest extends FormRequest
{
    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return array<string, array<int, \App\Rules\Updated|\Illuminate\Validation\Rules\Unique|\Illuminate\Validation\Rules\Date|string>>
     */
    public function rules(): array
    {
        $registrationNumberRegex = RegistrationNumber::pattern();

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
            'registration_number' => [
                'required',
                'string',
                'min:14',
                "regex:{$registrationNumberRegex}",
                new Updated($student->registration_number),
                Rule::unique(Student::class)->ignore($student->id),
            ],
            'remark' => ['required', 'string'],
        ];
    }
}
