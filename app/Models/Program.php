<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use softDeletes;

    protected $fillable = ['department_id', 'code', 'name'];

    /**
     * @return BelongsTo<Department, Program>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
