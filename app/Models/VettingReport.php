<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VettingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class VettingReport extends Model
{
    use SoftDeletes;

    public static function updateOrCreateUsingModel(
        Model $model,
        VettingStep $vettingStep,
        VettingStatus $vettingStatus,
        string $message,
    ): self {
        $modelName = $model::class;
        $vettableType = (new $modelName())->getMorphClass();

        $vettingReport = self::query()
            ->where('vettable_id', $model->id)
            ->where('vettable_type', $vettableType)
            ->where('vetting_step_id', $vettingStep->id)
            ->withTrashed()
            ->first();

        if ($vettingReport) {
            $vettingReport->restore();

            $vettingReport->message = $message;
            $vettingReport->status = $vettingStatus;
            $vettingReport->save();

            return $vettingReport;
        }

        $vettingReport = new self();
        $vettingReport->vettable_id = $model->id;
        $vettingReport->vettable_type = $vettableType;
        $vettingReport->vetting_step_id = $vettingStep->id;
        $vettingReport->message = $message;
        $vettingReport->status = $vettingStatus;
        $vettingReport->save();

        return $vettingReport;
    }

    public static function clearFailedReportForStep(VettingStep $vettingStep): void
    {
        if (! $vettingStep->failureReportExists()) {
            return;
        }

        self::query()
            ->where('vetting_step_id', $vettingStep->id)
            ->where('status', VettingStatus::FAILED)
            ->delete();
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<\Illuminate\Database\Eloquent\Model, \App\Models\VettingReport>
     */
    public function vettable(): MorphTo
    {
        return $this->MorphTo();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\VettingEvent, \App\Models\VettingReport> */
    public function vettingEvent(): BelongsTo
    {
        return $this->belongsTo(VettingEvent::class);
    }
}
