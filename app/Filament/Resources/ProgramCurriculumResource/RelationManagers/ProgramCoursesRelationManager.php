<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProgramCurriculumResource\RelationManagers;

use App\Enums\CreditUnitEnum;
use App\Models\Course;
use App\Models\CourseType;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ProgramCoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('course_id')->label('Course')
                    ->options(Course::query()->pluck('code', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('credit_unit')->label('Credit Unit')
                    ->options(CreditUnitEnum::class)
                    ->required(),
                Select::make('course_type_id')->label('Course Type')
                    ->options(CourseType::query()->pluck('name', 'id'))
                    ->required(),
            ]);
    }

    /** @throws \Exception */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('program_curriculum_id')
            ->columns([
                TextColumn::make('course.code')->sortable(),
                TextColumn::make('credit_unit'),
                TextColumn::make('courseType.name')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course_type')
                    ->relationship('courseType', 'name'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])->paginated(false);
    }
}
