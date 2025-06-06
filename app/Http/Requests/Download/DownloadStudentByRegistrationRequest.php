<?php

declare(strict_types=1);

namespace App\Http\Requests\Download;

use App\Values\RegistrationNumber;
use Illuminate\Foundation\Http\FormRequest;

final class DownloadStudentByRegistrationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<int,string>>
     */
    public function rules(): array
    {
        $registrationNumberRegex = RegistrationNumber::pattern();

        return [
            'registration_number' => [
                'required', 'string', 'min:14',
                "regex:{$registrationNumberRegex}",
            ],
        ];
    }
}
