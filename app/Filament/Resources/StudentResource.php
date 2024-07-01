<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('matriculation_number')->searchable()->sortable(),
                TextColumn::make('last_name')->searchable()->sortable(),
                TextColumn::make('first_name')->searchable()->sortable(),
                TextColumn::make('other_names')->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gender')->sortable(),
                TextColumn::make('date_of_birth'),
                TextColumn::make('program_id'),

            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'index' => Pages\ListStudents::route('/'),
        ];
    }
}
