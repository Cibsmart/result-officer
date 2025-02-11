<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramCurriculumResource\Pages;
use App\Models\ProgramCurriculum;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class ProgramCurriculumResource extends Resource
{
    protected static ?string $model = ProgramCurriculum::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Curriculum';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (ProgramCurriculum $record) => ProgramCurriculumLevelResource::getUrl(
                'index', ['record' => $record->id],
            ))
            ->columns([
                Tables\Columns\TextColumn::make('program.name')
                    ->numeric()->sortable()->searchable(),
                Tables\Columns\TextColumn::make('curriculum.code')
                    ->numeric()->sortable(),
                Tables\Columns\TextColumn::make('session.name')
                    ->numeric()->sortable()->searchable(),
                Tables\Columns\TextColumn::make('entry_mode')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('program')->relationship('program', 'name'),
                SelectFilter::make('Entry Session')->relationship('session', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('levels')
                    ->url(fn ($record) => ProgramCurriculumLevelResource::getUrl('index', ['record' => $record->id])),
            ]);
    }

    /** @return array<int, class-string|\Filament\Resources\RelationManagers\RelationGroup> */
    public static function getRelations(): array
    {
        return [];
    }

    /** @return array<string, \Filament\Resources\Pages\PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListProgramCurricula::route('/')];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
