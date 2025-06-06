<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\EntryMode;
use App\Enums\Gender;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers\EnrollmentsRelationManager;
use App\Filament\Resources\StudentResource\RelationManagers\ProgramRelationManager;
use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Students';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('registration_number')->required(),
                TextInput::make('last_name')->required(),
                TextInput::make('first_name')->required(),
                TextInput::make('other_names'),
                Select::make('gender')->options(Gender::class)->required(),
                DatePicker::make('date_of_birth')->maxDate(now()->subYears(15)),
                Select::make('program_id')
                    ->relationship('program', 'name')
                    ->searchable()->preload()
                    ->required(),
                Select::make('local_government_id')
                    ->relationship('lga', 'name')
                    ->required(),
                Select::make('entry_session_id')
                    ->relationship('entrySession', 'name')
                    ->searchable()->preload()
                    ->required()->label('Entry Session'),
                Select::make('entry_level_id')
                    ->options([1 => '100', 2 => '200'])
                    ->required()->label('Entry Level'),
                Select::make('entry_mode')
                    ->options(EntryMode::class)
                    ->required(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration_number')->searchable()->sortable(),
                TextColumn::make('last_name')->searchable()->sortable(),
                TextColumn::make('first_name')->searchable()->sortable(),
                TextColumn::make('other_names')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gender')->sortable(),
                TextColumn::make('date_of_birth')->date(),
                TextColumn::make('program.name')->searchable()->sortable(),
                TextColumn::make('lga.name')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('entrySession.name')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('entryLevel.name')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('entry_mode')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    /** @return array<int, class-string|\Filament\Resources\RelationManagers\RelationGroup> */
    public static function getRelations(): array
    {
        return [
            EnrollmentsRelationManager::class,
            ProgramRelationManager::class,
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
