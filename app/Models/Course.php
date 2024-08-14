<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

final class Course extends Model
{
    protected $fillable = ['code', 'title', 'online_id', 'active'];

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function name(): Attribute
    {
        return Attribute::make(
            /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter */
            get: fn (
                ?string $value,
                array $attributes,
            ): string => "({$attributes['code']}) {$attributes['title']}",
        );
    }
}
