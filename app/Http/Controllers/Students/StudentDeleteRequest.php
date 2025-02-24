<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StudentDeleteRequest extends FormRequest
{
    /** @return array<string, array<int, \Illuminate\Validation\Rules\Date|string>> */
    public function rules(): array
    {
        return [
            'has_mail' => ['required', 'boolean'],
            'mail_date' => [
                'exclude_if:has_mail,false',
                'required', 'string',
                'regex:/^20\d{2}-\d{2}-\d{2}$/',
                Rule::date()->todayOrBefore(),
            ],
            'mail_title' => ['exclude_if:has_mail,false', 'required', 'string', 'min:10', 'max:255'],
            'password' => ['required', 'current_password'],
            'remark' => ['required', 'string'],
            'student' => ['required', 'integer', 'exists:students,id'],
        ];
    }
}
