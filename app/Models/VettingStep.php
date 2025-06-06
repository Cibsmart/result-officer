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
            VettingReport::clearFailedReportForStep($vettingStep);

            return $vettingStep;
        }

        $vettingStep = new self();

        $vettingStep->vetting_event_id = $vettingEvent->id;
        $vettingStep->type = $vettingType->value;
        $vettingStep->status = VettingStatus::NEW;

        $vettingStep->save();

        return $vettingStep;
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\VettingEvent, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\VettingEvent, static>
     */
    public function vettingEvent(): BelongsTo
    {
        return $this->belongsTo(VettingEvent::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\VettingReport, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\VettingReport, static>
     */
    public function vettingReports(): HasMany
    {
        return $this->hasMany(VettingReport::class, 'vetting_step_id');
    }

    public function updateStatus(VettingStatus $status): void
    {
        if ($status === VettingStatus::PASSED) {
            VettingReport::clearFailedReportForStep($this);
        }

        $this->status = $status;
        $this->save();
    }

    public function failureReportExists(): bool
    {
        return $this->vettingReports()
            ->where('status', VettingStatus::FAILED)
            ->exists();
    }

    /** @return array{status: 'App\Enums\VettingStatus', type: 'App\Enums\VettingType'} */
    protected function casts(): array
    {
        return [
            'status' => VettingStatus::class,
            'type' => VettingType::class,
        ];
    }
}
