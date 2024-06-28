<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use Filament\Resources\Pages\EditRecord;

final class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    /** @return array<int, \Filament\Actions\Action|\Filament\Actions\ActionGroup> */
    protected function getHeaderActions(): array
    {
        return [

        ];
    }

}
