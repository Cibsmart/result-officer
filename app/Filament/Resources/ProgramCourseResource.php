<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramCurriculumCourseResource\Pages;
use App\Filament\Resources\ProgramCurriculumCourseResource\RelationManagers\CourseRelationManager;
use App\Filament\Resources\ProgramCurriculumCourseResource\RelationManagers\CourseTypeRelationManager;
use App\Filament\Resources\ProgramCurriculumCourseResource\RelationManagers\CreditUnitRelationManager;
use App\Filament\Resources\ProgramCurriculumCourseResource\RelationManagers\ProgramCurriculumRelationManager;
use App\Models\ProgramCourse;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ProgramCourseResource extends Resource
{
    protected static ?string $model = ProgramCourse::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Courses';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('program_curriculum_id')
                    ->searchable()->preload()->required()
                    ->relationship('programCurriculum', 'slug'),
                Select::make('course_id')
                    ->searchable()->preload()->required()
                    ->relationship('course', 'code'),
                Select::make('credit_unit_id')
                    ->searchable()->preload()->required()
                    ->relationship('creditUnit', 'value'),
                Select::make('course_type_id')
                    ->searchable()->preload()->required()
                    ->relationship('courseType', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('programCurriculum.slug')->numeric()->sortable(),
                TextColumn::make('course.code')->sortable(),
                TextColumn::make('creditUnit.value')->numeric()->sortable(),
                TextColumn::make('courseType.name')->numeric()->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    /** @return array<int, string> */
    public static function getRelations(): array
    {
        return [
            ProgramCurriculumRelationManager::class,
            CourseRelationManager::class,
            CreditUnitRelationManager::class,
            CourseTypeRelationManager::class,
        ];
    }

    /** @return array<string, \Filament\Resources\Pages\PageRegistration> */
    public static function getPages(): array
    {
        return [
            'create' => Pages\CreateProgramCurriculumCourse::route('/create'),
            'edit' => Pages\EditProgramCurriculumCourse::route('/{record}/edit'),
            'index' => Pages\ListProgramCurriculumCourses::route('/'),
        ];
    }
}
