<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VettingStatus;
use App\Enums\VettingType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class VettingStep extends Model
{
    public static function getOrCreateUsingVettingEvent(
        VettingEvent $vettingEvent,
        VettingType $vettingType,
    ): self {
        $vettingStep = self::query()
            ->where('vetting_event_id', $vettingEvent->id)
            ->where('type', $vettingType)
            ->first();

        if ($vettingStep) {
            return $vettingStep;
        }

        $vettingStep = new self();

        $vettingStep->vetting_event_id = $vettingEvent->id;
        $vettingStep->type = $vettingType->value;
        $vettingStep->status = VettingStatus::NEW;

        $vettingStep->save();

        return $vettingStep;
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\VettingEvent, \App\Models\VettingStep> */
    public function vettingEvent(): BelongsTo
    {
        return $this->belongsTo(VettingEvent::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\VettingReport, \App\Models\VettingStep> */
    public function vettingReports(): HasMany
    {
        return $this->hasMany(VettingReport::class, 'vetting_step_id');
    }

    public function updateStatus(VettingStatus $status): void
    {
        if ($status === VettingStatus::PASSED && $this->failureReports()) {
            VettingReport::clearFailedReportForStep($this);
        }

        $this->status = $status;
        $this->save();
    }

    /** @return array{status: 'App\Enums\VettingStatus', type: 'App\Enums\VettingType'} */
    protected function casts(): array
    {
        return [
            'status' => VettingStatus::class,
            'type' => VettingType::class,
        ];
    }

    private function failureReports(): bool
    {
        return $this->vettingReports()
            ->where('status', VettingStatus::FAILED)
            ->exists();
    }
}
