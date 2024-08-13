<?php

declare(strict_types=1);

namespace App\Http\Requests\Download;

use App\Models\Course;
use App\Models\Session;
use Illuminate\Foundation\Http\FormRequest;

final class DownloadRegistrationsBySessionCourseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<string>|string>
     */
    public function rules(): array
    {
        return [
            'course' => ['required', 'array'],
            'course.id' => ['required', 'integer', 'exists:course,id'],
            'session' => ['required', 'array'],
            'session.id' => ['required', 'integer', 'exists:academic_sessions,id'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->replace([
            'onlineCourseId' => Course::query()
                ->where('id', $this->input('course.id'))
                ->firstOrFail()
                ->online_id,

            'sessionName' => Session::query()
                ->where('id', $this->input('session.id'))
                ->firstOrFail()
                ->name,
        ]);
    }
}
