<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Months;
use App\Rules\AccessibleDepartment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ClearedIndexRequest extends FormRequest
{
    /** @return array<string, list<object|string>> */
    public function rules(): array
    {
        return [
            'department' => ['required', 'integer', 'exists:departments,id', new AccessibleDepartment()],
            'month' => ['required', Rule::enum(Months::class)],
            'year' => ['required', 'integer', 'regex:/^\d{4}$/'],
        ];
    }
}
