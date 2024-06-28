<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumCourseResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

final class CourseTypeRelationManager extends RelationManager
{

    protected static string $relationship = 'courseType';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('code'),
            ])
            ->filters([
            ]);
    }

}
