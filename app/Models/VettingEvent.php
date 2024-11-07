<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VettingEventStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class VettingEvent extends Model
{
    protected $fillable = ['student_id', 'program_curriculum_id', 'status'];

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

    /** @return array{status: 'App\Enums\VettingEventStatus'} */
    protected function casts(): array
    {
        return [
            'status' => VettingEventStatus::class,
        ];
    }
}
