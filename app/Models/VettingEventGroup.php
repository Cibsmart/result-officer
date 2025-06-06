<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VettingEventStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

final class VettingEventGroup extends Model
{
    public static function new(
        User $user,
        Department $department,
        string $title,
        VettingEventStatus $status = VettingEventStatus::QUEUED,
    ): self {
        $group = new self();

        $group->user_id = $user->id;
        $group->department_id = $department->id;
        $group->title = $title;
        $group->status = $status;
        $group->slug = Str::slug($title);

        $group->save();

        return $group;
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Department, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Department, static>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Student> $students */
    public function addStudents(User $user, Collection $students): void
    {
        $vettingIds = [];

        foreach ($students as $student) {
            $vettingIds[] = VettingEvent::getOrCreateUsingStudent($student, $user)->id;

        }

        $this->vettingEvents()->sync($vettingIds);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\VettingEvent, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\VettingEvent, static>
     */
    public function vettingEvents(): BelongsToMany
    {
        return $this->belongsToMany(VettingEvent::class,
            'vetting_event_group_vetting_event',
            'vetting_event_group_id',
            'vetting_event_id',
        );
    }

    public function updateStatus(VettingEventStatus $status): void
    {
        $this->status = $status;
        $this->save();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return array{status: 'App\Enums\VettingEventStatus'} */
    protected function casts(): array
    {
        return [
            'status' => VettingEventStatus::class,
        ];
    }
}
