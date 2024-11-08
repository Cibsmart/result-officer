<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VettingStatus;
use App\Enums\VettingType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class VettingStep extends Model
{
    protected $fillable = ['vetting_event_id', 'type', 'status'];

    public static function getOrCreateUsingVettingEvent(
        VettingEvent $vettingEvent,
        VettingType $vettingType,
    ): self {
        return self::query()->firstOrCreate(
            ['vetting_event_id' => $vettingEvent->id, 'type' => $vettingType],
            ['status' => VettingStatus::NEW],
        );
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\VettingReport, \App\Models\VettingStep> */
    public function reports(): HasMany
    {
        return $this->hasMany(VettingReport::class, 'vetting_step_id');
    }

    public function updateStatus(VettingStatus $status): void
    {
        $this->status = $status;
        $this->save();
    }

    public function updateStatusAndRemarks(VettingStatus $status, string $remarks): void
    {
        $this->status = $status;
        $this->remarks = $remarks;
        $this->save();
    }

    /** @return array{status: 'App\Enums\VettingStatus'} */
    protected function casts(): array
    {
        return [
            'status' => VettingStatus::class,
        ];
    }
}
