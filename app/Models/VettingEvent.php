<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VettingEventStatus;
use App\Enums\VettingStatus;
use App\Events\VettingStatusUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Event;

final class VettingEvent extends Model
{
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

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Student, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Student, static>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\VettingStep, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\VettingStep, static>
     */
    public function vettingSteps(): HasMany
    {
        return $this->hasMany(VettingStep::class);
    }

    public function updateStatus(VettingEventStatus $status): void
    {
        $this->status = $status;
        $this->save();

        $student = $this->student;
        assert($student instanceof Student);

        Event::dispatch(new VettingStatusUpdated($student));
    }

    public function updateVettingStatus(): void
    {
        $failed = $this->vettingSteps()
            ->where('status', VettingStatus::FAILED)
            ->exists();

        $status = $failed
            ? VettingEventStatus::FAILED
            : VettingEventStatus::PASSED;

        $this->updateStatus($status);

        $this->save();
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\VettingEventGroup, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\VettingEventGroup, static>
     */
    public function vettingEventGroups(): BelongsToMany
    {
        return $this->belongsToMany(VettingEventGroup::class,
            'vetting_event_group_vetting_event',
            'vetting_event_id',
            'vetting_event_group_id',
        );
    }

    /** @return array{status: 'App\Enums\VettingEventStatus'} */
    protected function casts(): array
    {
        return [
            'status' => VettingEventStatus::class,
        ];
    }
}
