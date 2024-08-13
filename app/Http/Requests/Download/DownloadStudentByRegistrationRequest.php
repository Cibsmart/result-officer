<?php

declare(strict_types=1);

namespace App\Http\Requests\Download;

use Illuminate\Foundation\Http\FormRequest;

final class DownloadStudentByRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<int,string>>
     */
    public function rules(): array
    {
        return [
            'registration_number' => [
                'required', 'string', 'min:14',
                'regex:/^EBSU\/\d{4}\/\d{4,6}[A-Z]?$/',
            ],
        ];
    }
}
