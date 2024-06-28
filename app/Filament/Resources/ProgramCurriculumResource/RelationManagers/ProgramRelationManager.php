<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

final class ProgramRelationManager extends RelationManager
{
    protected static string $relationship = 'program';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
            ]);
    }

}
