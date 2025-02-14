<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\RecordsUnitHeadResource\Pages;
use App\Models\RecordsUnitHead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

final class RecordsUnitHeadResource extends Resource
{
    protected static ?string $model = RecordsUnitHead::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_current')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_current')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    /** @return array<string, \Filament\Resources\Pages\PageRegistration> */
    public static function getPages(): array
    {
        return [
            'create' => Pages\CreateRecordsUnitHead::route('/create'),
            'edit' => Pages\EditRecordsUnitHead::route('/{record}/edit'),
            'index' => Pages\ListRecordsUnitHeads::route('/'),
        ];
    }
}
