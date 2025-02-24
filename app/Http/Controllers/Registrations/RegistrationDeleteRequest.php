<?php

declare(strict_types=1);

namespace App\Http\Controllers\Registrations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class RegistrationDeleteRequest extends FormRequest
{
    /** @return array<string, array<int, \Illuminate\Validation\Rules\Date|string>> */
    public function rules(): array
    {
        return [
            'mail_date' => [
                'required',
                'string',
                'regex:/^20\d{2}-\d{2}-\d{2}$/',
                Rule::date()->todayOrBefore(),
            ],
            'mail_title' => ['required', 'string', 'min:10', 'max:255'],
            'password' => ['required', 'current_password'],
            'remark' => ['required', 'string'],
            'result' => ['required', 'integer', 'exists:registrations,id'],
        ];
    }
}
