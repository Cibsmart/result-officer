<?php

declare(strict_types=1);

namespace App\Http\Requests\Results;

use Illuminate\Foundation\Http\FormRequest;

final class ResultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'matriculation_number' => [
                'required',
                'string',
                'min:14',
                'regex:/^EBSU\/\d{4}\/\d{4,6}[A-Z]?$/',
                'exists:students,matriculation_number',
            ],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'exists' => 'The matriculation number does not exist.',
        ];
    }
}
