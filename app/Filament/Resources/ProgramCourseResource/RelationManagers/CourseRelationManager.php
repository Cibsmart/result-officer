<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumCourseResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

final class CourseRelationManager extends RelationManager
{
    protected static string $relationship = 'course';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('title'),
            ])
            ->filters([
            ]);
    }

}
