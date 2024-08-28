<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramCurriculumResource\Pages;
use App\Filament\Resources\ProgramCurriculumResource\RelationManagers\ProgramCoursesRelationManager;
use App\Models\ProgramCurriculum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ProgramCurriculumResource extends Resource
{
    protected static ?string $model = ProgramCurriculum::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Courses';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('program_id')
                    ->relationship('program', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('curriculum_id')->relationship('curriculum', 'code')->required(),
                Select::make('session_id')->relationship('session', 'name')->required(),
                Select::make('level_id')->relationship('level', 'name')->required(),
                Select::make('semester_id')->relationship('semester', 'name')->required(),
                TextInput::make('minimum_elective_units')->required()->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('program.name')->sortable()->searchable(),
                TextColumn::make('curriculum.code')->sortable(),
                TextColumn::make('session.name')->sortable(),
                TextColumn::make('level.name')->sortable(),
                TextColumn::make('semester.name')->sortable(),
                TextColumn::make('minimum_elective_units')->numeric(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    /** @return array<int, class-string> */
    public static function getRelations(): array
    {
        return [
            ProgramCoursesRelationManager::class,
        ];
    }

    /** @return array<string, \Filament\Resources\Pages\PageRegistration> */
    public static function getPages(): array
    {
        return [
            'create' => Pages\CreateProgramCurriculum::route('/create'),
            'edit' => Pages\EditProgramCurriculum::route('/{record}/edit'),
            'index' => Pages\ListProgramCurricula::route('/'),
        ];
    }
}
