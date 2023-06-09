<?php

namespace App\Filament\Resources\ChatbotResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgentsRelationManager extends RelationManager
{
    protected static string $relationship = 'agents';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('prompt')
                    ->rows(10)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('classification')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('model')
                    ->default('gpt-3.5-turbo'),

                Forms\Components\TextInput::make('max_tokens')
                    ->default(32)
                    ->numeric(),

                Forms\Components\TextInput::make('temperature')
                    ->default(0.0)
                    ->minValue(0)
                    ->maxValue(1)
                    ->step(.01)
                    ->numeric(),

                Forms\Components\TextInput::make('presence_penalty')
                    ->default(0.0)
                    ->minValue(0)
                    ->maxValue(1)
                    ->step(.01)
                    ->numeric(),

                Forms\Components\TextInput::make('frequency_penalty')
                    ->default(0.0)
                    ->minValue(0)
                    ->maxValue(1)
                    ->step(.01)
                    ->numeric(),

                Forms\Components\TextInput::make('stop')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
