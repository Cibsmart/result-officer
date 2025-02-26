<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VettingEventStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

final class VettingEventGroup extends Model
{
    public static function new(
        User $user,
        Department $department,
        VettingEventStatus $status = VettingEventStatus::QUEUED,
    ): self {
        $group = new self();

        $group->user_id = $user->id;
        $group->department_id = $department->id;
        $group->status = $status;

        $group->save();

        return $group;
    }

    /** @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Student> $students */
    public function addStudents(User $user, Collection $students): void
    {
        $vettings = [];

        foreach ($students as $student) {
            $vettingEvent = VettingEvent::getOrCreateUsingStudent($student, $user);
        }
    }

    /** @return array{status: 'App\Enums\VettingEventStatus'} */
    protected function casts(): array
    {
        return [
            'status' => VettingEventStatus::class,
        ];
    }
}
