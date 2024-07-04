<?php

declare(strict_types=1);

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'results';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('course')
            ->columns([
                TextColumn::make('enrollment.session.name')->sortable()->searchable(),
                TextColumn::make('semester.name')->sortable()->searchable(),
                TextColumn::make('course.code')->sortable()->searchable(),
                TextColumn::make('creditUnit.value'),
                TextColumn::make('creditUnit.value'),
                TextColumn::make('total_score'),
                TextColumn::make('grade'),
                TextColumn::make('courseStatus.name')->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
