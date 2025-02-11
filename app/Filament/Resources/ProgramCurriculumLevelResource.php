<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramCurriculumLevelResource\Pages;
use App\Models\ProgramCurriculumLevel;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class ProgramCurriculumLevelResource extends Resource
{
    protected static ?string $model = ProgramCurriculumLevel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Curriculum';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (ProgramCurriculumLevel $record) => ProgramCurriculumSemesterResource::getUrl(
                'index', ['record' => $record->id],
            ))
            ->modifyQueryUsing(fn (Builder $query) => $query->where('program_curriculum_id',
                request()->query('record')))
            ->columns([
                Tables\Columns\TextColumn::make('programCurriculum.curriculum.code')
                    ->sortable()->label('Curriculum'),
                Tables\Columns\TextColumn::make('programCurriculum.entry_mode')
                    ->sortable()->label('Entry Mode'),
                Tables\Columns\TextColumn::make('programCurriculum.session.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('level.name')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('semesters')
                    ->url(fn ($record) => ProgramCurriculumSemesterResource::getUrl('index',
                        ['record' => $record->id])),
            ]);
    }

    /** @return array<int, class-string|\Filament\Resources\RelationManagers\RelationGroup> */
    public static function getRelations(): array
    {
        return [
        ];
    }

    /** @return array<string, \Filament\Resources\Pages\PageRegistration> */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProgramCurriculumLevels::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getHeading(): string
    {
        return 'Levels';
    }
}
