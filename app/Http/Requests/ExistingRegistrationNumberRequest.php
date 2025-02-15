<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Student;
use App\Values\RegistrationNumber;
use Illuminate\Foundation\Http\FormRequest;

final class ExistingRegistrationNumberRequest extends FormRequest
{
    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<string>|string> */
    public function rules(): array
    {
        $registrationNumberRegex = RegistrationNumber::pattern();

        return [
            'registration_number' => [
                'required', 'string', 'min:14',
                'exists:students,registration_number',
                "regex:{$registrationNumberRegex}",
            ],
        ];
    }

    protected function passedValidation(): void
    {
        $this->replace([
            'registration_number' => $this->input('registration_number'),
            'student' => Student::getUsingRegistrationNumber($this->input('registration_number')),
        ]);
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'exists' => 'The registration number does not exist.',
        ];
    }
}
