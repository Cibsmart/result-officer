<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class UserDepartment extends Model
{
    use SoftDeletes;

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Department, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Department, static>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
