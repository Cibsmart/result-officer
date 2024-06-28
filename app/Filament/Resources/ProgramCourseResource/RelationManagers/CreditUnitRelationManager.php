<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumCourseResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

final class CreditUnitRelationManager extends RelationManager
{

    protected static string $relationship = 'creditUnit';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('value')
            ->columns([
                Tables\Columns\TextColumn::make('value'),
            ])
            ->filters([
            ]);
    }

}
