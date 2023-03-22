<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatbotResource\Pages;
use App\Filament\Resources\ChatbotResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use SavvyAI\Models\Chatbot;

class ChatbotResource extends Resource
{
    protected static ?string $model = Chatbot::class;

    protected static ?string $navigationIcon = 'heroicon-o-lightning-bolt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Forms\Components\Select::make('trainable_id')
                            ->relationship('trainable', 'name')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('prompt')
                            ->rows(10)
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
                    ])
                    ->columns(2)
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
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AgentsRelationManager::class,
            RelationManagers\DialoguesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListChatbots::route('/'),
            'create' => Pages\CreateChatbot::route('/create'),
            'edit'   => Pages\EditChatbot::route('/{record}/edit'),
        ];
    }
}
