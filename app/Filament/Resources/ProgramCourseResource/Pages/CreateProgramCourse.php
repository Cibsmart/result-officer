<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCourseResource\Pages;

use App\Filament\Resources\ProgramCourseResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateProgramCourse extends CreateRecord
{
    protected static string $resource = ProgramCourseResource::class;
}
