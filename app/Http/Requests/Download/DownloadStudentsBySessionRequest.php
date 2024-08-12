<?php

declare(strict_types=1);

namespace App\Http\Requests\Download;

use App\Models\Session;
use Illuminate\Foundation\Http\FormRequest;

final class DownloadStudentsBySessionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<string>>
     */
    public function rules(): array
    {
        return [
            'session' => ['required', 'array'],
            'session.id' => ['required', 'integer', 'exists:academic_sessions,id'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->replace([
            'sessionName' => Session::query()
                ->where('id', $this->input('session.id'))
                ->firstOrFail()
                ->name,
        ]);
    }
}
