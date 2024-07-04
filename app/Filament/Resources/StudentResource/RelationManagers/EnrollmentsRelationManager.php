<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('year')
            ->columns([
                Tables\Columns\TextColumn::make('year.name'),
                Tables\Columns\TextColumn::make('session.name'),
                Tables\Columns\TextColumn::make('level.name'),
            ])->paginated(false);
    }
}
