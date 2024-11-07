<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VettingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class VettingStep extends Model
{
    protected $fillable = ['vetting_event_id', 'type', 'status'];

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

    /** @return array{status: 'App\Enums\VettingStatus'} */
    protected function casts(): array
    {
        return [
            'status' => VettingStatus::class,
        ];
    }
}
