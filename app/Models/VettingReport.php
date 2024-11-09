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

    protected $fillable = ['vetting_step_id', 'vettable_id', 'vettable_type', 'status'];

    public static function updateOrCreateUsingModel(
        Model $model,
        VettingStep $vettingStep,
        VettingStatus $vettingStatus,
    ): self {
        $modelName = $model::class;

        return $model->vettable()->updateOrCreate(
            [
                'vettable_id' => $model->id,
                'vettable_type' => (new $modelName())->getMorphClass(),
                'vetting_step_id' => $vettingStep->id,
            ],
            ['status' => $vettingStatus],
        );
    }

    public static function clearFailedReportForStep(VettingStep $vettingStep): void
    {
        self::query()
            ->where('vetting_step_id', $vettingStep->id)
            ->where('status', VettingStatus::FAILED)
            ->delete();
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<\Illuminate\Database\Eloquent\Model, \App\Models\VettingReport>
     */
    public function vettingReport(): MorphTo
    {
        return $this->MorphTo();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\VettingEvent, \App\Models\VettingReport> */
    public function vettingEvent(): BelongsTo
    {
        return $this->belongsTo(VettingEvent::class);
    }
}
