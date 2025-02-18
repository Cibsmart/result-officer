<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Rules\Updated;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class StudentNameRequest extends FormRequest
{
    /** @return array<string, array<int, \App\Rules\Updated|\Illuminate\Validation\Rules\Date|string>> */
    public function rules(): array
    {
        $student = $this->route('student');

        return [
            'first_name' => ['required', 'string', 'min:2'],
            'has_mail' => ['required', 'boolean'],
            'last_name' => ['required', 'string', 'min:2'],
            'mail_date' => [
                'exclude_if:has_mail,false',
                'required',
                'regex:/^20\d{2}-\d{2}-\d{2}$/',
                Rule::date()->todayOrBefore(),
            ],
            'mail_title' => ['exclude_if:has_mail,false', 'required', 'string', 'min:10', 'max:255'],
            'name' => ['required', 'string', 'min:2', new Updated($student->name)],
            'other_names' => ['nullable', 'string'],
            'remark' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => Str::of("{$this->last_name} {$this->first_name} {$this->other_names}")->trim()->upper()->value(),
        ]);
    }
}
