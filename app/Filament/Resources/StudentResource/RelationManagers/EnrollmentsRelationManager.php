<?php

declare(strict_types=1);

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

final class EnrollmentsRelationManager extends RelationManager
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
