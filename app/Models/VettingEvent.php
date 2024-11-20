<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VettingEventStatus;
use App\Enums\VettingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class VettingEvent extends Model
{
    protected $fillable = ['user_id', 'student_id', 'program_curriculum_id', 'status'];

    public static function getOrCreateUsingStudent(Student $student, User $user): self
    {
        $programCurriculum = $student->programCurriculum();
        $programCurriculumId = $programCurriculum
            ? $programCurriculum->id
            : null;

        return self::query()->firstOrCreate(
            ['student_id' => $student->id],
            [
                'program_curriculum_id' => $programCurriculumId,
                'status' => VettingEventStatus::NEW,
                'user_id' => $user->id,
            ],
        );
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Student, \App\Models\VettingEvent> */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\VettingStep, \App\Models\VettingEvent> */
    public function vettingSteps(): HasMany
    {
        return $this->hasMany(VettingStep::class);
    }

    public function updateStatus(VettingEventStatus $status): void
    {
        $this->status = $status;
        $this->save();
    }

    public function updateVettingStatus(): void
    {
        $failed = $this->vettingSteps()
            ->where('status', VettingStatus::FAILED)
            ->exists();

        $this->status = $failed
            ? VettingEventStatus::FAILED
            : VettingEventStatus::PASSED;

        $this->save();
    }

    /** @return array{status: 'App\Enums\VettingEventStatus'} */
    protected function casts(): array
    {
        return [
            'status' => VettingEventStatus::class,
        ];
    }
}
