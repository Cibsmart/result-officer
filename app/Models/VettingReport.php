<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class VettingReport extends Model
{
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
