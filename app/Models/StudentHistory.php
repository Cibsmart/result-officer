<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\RecordActionType;
use App\Enums\RecordSource;
use Illuminate\Database\Eloquent\Model;

final class StudentHistory extends Model
{
    /** @param array<string, string> $data */
    public static function createNewUpdate(
        Student $student,
        Model $model,
        StudentModifiableField $updatedField,
        array $data,
        string $remark = '',
        RecordSource $source = RecordSource::USER,
    ): self {
        $modelName = $model::class;
        $modifiableType = (new $modelName())->getMorphClass();

        $history = new self();

        $history->student_id = $student->id;
        $history->modifiable_id = $model->id;
        $history->modifiable_type = $modifiableType;
        $history->action = RecordActionType::UPDATE;
        $history->field = $updatedField;
        $history->data = $data;
        $history->remark = $remark;
        $history->source = $source;

        $history->save();

        return $history;
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return array{action: 'App\Enums\RecordActionType', data: 'array', field: 'App\Enums\ModifiableFields\StudentModifiableField', source: 'App\Enums\RecordSource'}
     */
    protected function casts(): array
    {
        return [
            'action' => RecordActionType::class,
            'data' => 'array',
            'field' => StudentModifiableField::class,
            'source' => RecordSource::class,
        ];
    }
}
