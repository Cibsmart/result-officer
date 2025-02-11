<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumSemesterResource\RelationManagers;

use App\Enums\CourseType;
use App\Enums\CreditUnit;
use App\Models\Course;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

final class ProgramCurriculumCoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'programCurriculumCourses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('course_id')
                    ->relationship('course', 'code')
                    ->getOptionLabelFromRecordUsing(fn (Course $record) => "{$record->code} - {$record->title}")
                    ->label('Course')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('credit_unit')
                    ->options(CreditUnit::class)
                    ->label('Credit Unit')
                    ->required(),

                Select::make('course_type')
                    ->options(CourseType::class)
                    ->label('Course Type')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('course_id')
            ->columns([
                Tables\Columns\TextColumn::make('course.code')->label('Course Code'),
                Tables\Columns\TextColumn::make('course.title')->label('Course Title'),
                Tables\Columns\TextColumn::make('credit_unit.value')->label('Credit Unit'),
                Tables\Columns\TextColumn::make('course_type.name')->label('Course Type'),
            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
