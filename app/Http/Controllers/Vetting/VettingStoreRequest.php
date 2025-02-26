<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vetting;

use App\Models\Department;
use App\Models\Student;
use App\Values\RegistrationNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

final class VettingStoreRequest extends FormRequest
{
    /** @return array<string, array<string>|string> */
    public function rules(): array
    {
        return [
            'department' => ['required', 'integer', 'exists:departments,id'],
            'registration_numbers' => ['required'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator): void {

            $registrationNumbers = $this->registration_numbers;

            [$invalidMessage, $invalidNumbers] = $this->validateRegistrationNumbers($registrationNumbers);

            $department = Department::getUsingId($this->department);
            $validRegistrationNumbers = $registrationNumbers->diff($invalidNumbers);

            $numberNotInDept = $this->checkDepartment($department, $validRegistrationNumbers);

            if ($invalidMessage === '' && $numberNotInDept === '') {
                return;
            }

            $validator->errors()->add('registration_numbers', "{$invalidMessage}. {$numberNotInDept}");
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'registration_numbers' => Str::of($this->registration_numbers)
                ->replace("\n", ',')
                ->replace(' ', '')
                ->explode(',')
                ->filter()
                ->unique(),
        ]);
    }

    /**
     * @param \Illuminate\Support\Collection<int, non-falsy-string> $registrationNumbers
     * @return  array{string, \Illuminate\Support\Collection<int, non-falsy-string>}
     */
    private function validateRegistrationNumbers(Collection $registrationNumbers): array
    {
        $invalidNumbers = $registrationNumbers->filter(fn (string $number) => ! RegistrationNumber::isValid($number));

        return $invalidNumbers->isNotEmpty()
            ? ['The following registration numbers are invalid: ' . $invalidNumbers->join(', '), $invalidNumbers]
            : ['', collect()];
    }

    /**
     * @param \Illuminate\Support\Collection<int, non-falsy-string> $registrationNumbers
     * @return  \Illuminate\Support\Collection<int, non-falsy-string>
     */
    private function checkDepartment(Department $department, Collection $registrationNumbers): string
    {
        $programIdsInDepartment = $department->programs->pluck('id');

        $students = Student::query()
            ->whereIn('registration_number', $registrationNumbers)
            ->whereIn('program_id', $programIdsInDepartment)
            ->pluck('registration_number');

        $numbersNotInDept = $registrationNumbers->diff($students);

        return $numbersNotInDept->isNotEmpty()
            ? "The following registration numbers are not in {$department->name}: " . $numbersNotInDept->join(', ')
            : '';
    }
}
