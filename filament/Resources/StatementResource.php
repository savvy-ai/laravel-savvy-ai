<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StatementResource\Pages;
use App\Filament\Resources\StatementResource\RelationManagers;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use SavvyAI\Models\Statement;

class StatementResource extends Resource
{
    protected static ?string $model = Statement::class;

    protected static ?string $navigationIcon = 'heroicon-o-database';

    protected static ?string $pluralModelLabel = 'Trained Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('statement')
                    ->required()
                    ->placeholder('Enter a statement'),

                Forms\Components\Select::make('trainable_id')
                    ->relationship('trainable', 'name')
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('statement')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('trainable.name')
                    ->label('Trainable'),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStatements::route('/'),
            'create' => Pages\CreateStatement::route('/create'),
            'edit'   => Pages\EditStatement::route('/{record}/edit'),
        ];
    }
}
