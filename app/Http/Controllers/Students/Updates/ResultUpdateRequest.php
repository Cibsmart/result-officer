<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Enums\CreditUnit;
use App\Models\Registration;
use App\Models\Student;
use App\Values\TotalScore;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class ResultUpdateRequest extends FormRequest
{
    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return array<string, array<int, \App\Rules\Updated|\Illuminate\Validation\Rules\Date|\Illuminate\Validation\Rules\Enum|string>>
     */
    public function rules(): array
    {
        $student = $this->route('student');
        assert($student instanceof Student);

        return [
            'credit_unit' => ['required', Rule::enum(CreditUnit::class)],
            'exam' => ['nullable', 'integer', 'min:0', 'max:100'],
            'has_mail' => ['required', 'boolean'],
            'in_course' => ['required', 'integer', 'min:0', 'max:50'],
            'in_course_2' => ['required', 'integer', 'min:0', 'max:50'],
            'mail_date' => [
                'exclude_if:has_mail,false',
                'required',
                'regex:/^20\d{2}-\d{2}-\d{2}$/',
                Rule::date()->todayOrBefore(),
            ],
            'mail_title' => ['exclude_if:has_mail,false', 'required', 'string', 'min:10', 'max:255'],
            'password' => ['required', 'current_password'],
            'registration_id' => ['required', 'integer', 'exists:registrations,id'],
            'remark' => ['required', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator): void {
            $registration = Registration::getUsingId($this->validated('registration_id'));

            $oldResultText = $registration->getUpdateData();
            $newResultText = "{$this->validated('credit_unit')}-{$this->validated('in_course')}-";
            $newResultText .= "{$this->validated('in_course_2')}-{$this->validated('exam')}";

            if ($oldResultText !== $newResultText) {
                return;
            }

            $validator->errors()->add('result', 'The result has not changed.');
        })->after(function ($validator): void {
            $total = $this->validated('in_course') + $this->validated('in_course_2') + $this->validated('exam');

            if (TotalScore::isValid($total)) {
                return;
            }

            $validator->errors()->add('result', 'Total score must be between 0 and 100.');
        });
    }
}
