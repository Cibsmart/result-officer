<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class VettingEvent extends Model
{
    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Student, \App\Models\VettingEvent> */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\VettingReport, \App\Models\VettingEvent> */
    public function reports(): HasMany
    {
        return $this->hasMany(VettingReport::class);
    }
}
