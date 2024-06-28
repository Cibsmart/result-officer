<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ProgramCurriculumCoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'programCurriculumCourses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('program_curriculum_id')->required(),
                TextInput::make('course_id')->required(),
                TextInput::make('credit_unit_id')->required(),
                TextInput::make('course_type_id')->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('program_curriculum_id')
            ->columns([
                TextColumn::make('program_curriculum_id'),
                TextColumn::make('course_id'),
                TextColumn::make('credit_unit_id'),
                TextColumn::make('course_type_id'),
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
