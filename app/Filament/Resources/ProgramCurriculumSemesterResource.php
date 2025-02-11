<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramCurriculumSemesterResource\Pages;
use App\Filament\Resources\ProgramCurriculumSemesterResource\RelationManagers\ProgramCurriculumCoursesRelationManager;
use App\Models\ProgramCurriculumSemester;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class ProgramCurriculumSemesterResource extends Resource
{
    protected static ?string $model = ProgramCurriculumSemester::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Curriculum';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('program_curriculum_level_id')
                    ->relationship('programCurriculumLevel', 'id')
                    ->required()->disabled(),
                Forms\Components\Select::make('semester_id')
                    ->relationship('semester', 'name')
                    ->required(),
                Forms\Components\TextInput::make('minimum_elective_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('minimum_elective_units')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('minimum_credit_units')
                    ->required()
                    ->numeric()
                    ->default(15),
                Forms\Components\TextInput::make('maximum_credit_units')
                    ->required()
                    ->numeric()
                    ->default(24),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('program_curriculum_level_id',
                request()->query('record')))
            ->columns([
                Tables\Columns\TextColumn::make('programCurriculumLevel.programCurriculum.session.name')
                    ->sortable()->label('Entry Session'),
                Tables\Columns\TextColumn::make('programCurriculumLevel.level.name')->sortable(),
                Tables\Columns\TextColumn::make('semester.name')->sortable(),
                Tables\Columns\TextColumn::make('minimum_elective_count')
                    ->numeric()->sortable()->label('Elective Count'),
                Tables\Columns\TextColumn::make('minimum_elective_units.value')
                    ->numeric()->sortable()->label('Elective Units'),
                Tables\Columns\TextColumn::make('minimum_credit_units')
                    ->numeric()->sortable()->label('Min CLoad'),
                Tables\Columns\TextColumn::make('maximum_credit_units')
                    ->numeric()->sortable()->label('Max CLoad'),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /** @return array<int, class-string|\Filament\Resources\RelationManagers\RelationGroup> */
    public static function getRelations(): array
    {
        return [
            ProgramCurriculumCoursesRelationManager::class,
        ];
    }

    /** @return array<string, \Filament\Resources\Pages\PageRegistration> */
    public static function getPages(): array
    {
        return [
            'create' => Pages\CreateProgramCurriculumSemester::route('/create'),
            'edit' => Pages\EditProgramCurriculumSemester::route('/{record}/edit'),
            'index' => Pages\ListProgramCurriculumSemesters::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
