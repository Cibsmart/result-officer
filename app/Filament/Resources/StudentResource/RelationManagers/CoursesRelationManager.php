<?php

declare(strict_types=1);

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('course')
            ->columns([
                TextColumn::make('semesterEnrollment.enrollment.session.name')->sortable()->searchable(),
                TextColumn::make('semesterEnrollment.semester.name')->sortable()->searchable(),
                TextColumn::make('course.code')->sortable()->searchable(),
                TextColumn::make('credit_unit'),
                TextColumn::make('result.total_score'),
                TextColumn::make('result.grade'),
                TextColumn::make('courseStatus.name')->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
