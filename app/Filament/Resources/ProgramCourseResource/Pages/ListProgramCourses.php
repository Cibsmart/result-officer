<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCourseResource\Pages;

use App\Filament\Resources\ProgramCourseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListProgramCourses extends ListRecords
{
    protected static string $resource = ProgramCourseResource::class;

    /** @return array<int, \Filament\Actions\Action> */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction ::make(),
        ];
    }
}
