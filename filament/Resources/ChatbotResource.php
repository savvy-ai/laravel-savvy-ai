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
use SavvyAI\Models\Chatbot;

class ChatbotResource extends Resource
{
    protected static ?string $model = Chatbot::class;

    protected static ?string $navigationIcon = 'heroicon-o-lightning-bolt';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Forms\Components\Select::make('trainable_id')
                            ->relationship('trainable', 'name')
                            ->columnSpanFull()
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
