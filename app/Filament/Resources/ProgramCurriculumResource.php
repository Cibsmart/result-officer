<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\EntryMode;
use App\Filament\Resources\ProgramCurriculumResource\Pages;
use App\Models\ProgramCurriculum;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class ProgramCurriculumResource extends Resource
{
    protected static ?string $model = ProgramCurriculum::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Curriculum';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('program_id')
                    ->relationship('program', 'name')
                    ->required(),
                Forms\Components\Select::make('curriculum_id')
                    ->relationship('curriculum', 'name')
                    ->required(),
                Forms\Components\Select::make('entry_session_id')
                    ->relationship('session', 'name',
                        modifyQueryUsing: fn (Builder $query,
                        ) => $query->orderBy('name', 'desc'))
                    ->required()
                    ->native(false),
                Select::make('entry_mode')
                    ->required()
                    ->options(EntryMode::class)
                    ->required(),
            ]);
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
            ->actions([
                Tables\Actions\Action::make('levels')
                    ->url(fn ($record) => ProgramCurriculumLevelResource::getUrl('index', ['record' => $record->id])),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListProgramCurricula::route('/')];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
