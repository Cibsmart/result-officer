<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExistingRegistrationNumberRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<string>|string>
     */
    public function rules(): array
    {
        return [
            'registration_number' => [
                'required', 'string', 'min:14',
                'exists:students,registration_number',
                'regex:/^EBSU\/\d{4}\/\d{4,6}[A-C]?$/',
            ],
        ];
    }
}
