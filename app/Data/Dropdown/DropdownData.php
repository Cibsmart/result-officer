<?php

declare(strict_types=1);

namespace App\Data\Dropdown;

use BackedEnum;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

final class DropdownData extends Data
{
    public function __construct(
        public readonly string $value,
        public readonly string $label,
    ) {
    }

    public static function fromModel(Model $model): self
    {
        if (! isset($model->id) || ! isset($model->name)) {
            return new self('', '');
        }

        $id = (string) $model->id;

        $name = $model->name;

        return new self(value: $id, label: $name);
    }

    public static function fromEnum(BackedEnum $enum): self
    {
        return new self(value: (string) $enum->value, label: $enum->name);
    }
}
