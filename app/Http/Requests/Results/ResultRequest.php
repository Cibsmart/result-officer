<?php

declare(strict_types=1);

namespace App\Http\Requests\Results;

use Illuminate\Foundation\Http\FormRequest;

final class ResultRequest extends FormRequest
{
    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'registration_number' => [
                'required',
                'string',
                'min:14',
                'regex:/^EBSU\/\d{4}\/\d{4,6}[A-Z]?$/',
                'exists:students,registration_number',
            ],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'exists' => 'The registration number does not exist.',
        ];
    }
}
