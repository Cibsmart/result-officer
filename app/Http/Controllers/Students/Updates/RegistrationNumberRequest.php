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
    /** @return array<string, array<int, \App\Rules\Updated|\Illuminate\Validation\Rules\Unique|string>> */
    public function rules(): array
    {
        $registrationNumberRegex = RegistrationNumber::pattern();
        $student = $this->route('student');

        assert($student instanceof Student);

        return [
            'registration_number' => [
                'required',
                'string',
                'min:14',
                "regex:{$registrationNumberRegex}",
                new Updated($student->registration_number),
                Rule::unique(Student::class)->ignore($student->id),
            ],
        ];
    }
}
