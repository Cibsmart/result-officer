<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ResultDetail extends Model
{
    /** @var array<int, string> */
    protected $hidden = ['data'];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Result, \App\Models\ResultDetail> */
    public function result(): BelongsTo
    {
        return $this->belongsTo(Result::class);
    }

    public function validate(): void
    {
        $this->data = $this->value;
        $this->validate = false;
        $this->save();
    }

    public function invalidate(): void
    {
        $this->validate = false;
        $this->save();
    }

    /** @return array{data: 'hashed', validate: 'boolean'} */
    protected function casts(): array
    {
        return [
            'data' => 'hashed',
            'validate' => 'boolean',
        ];
    }
}
