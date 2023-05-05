<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainableResource\Pages;
use App\Filament\Resources\TrainableResource\RelationManagers;
use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use SavvyAI\Models\Trainable;

class TrainableResource extends Resource
{
    protected static ?string $model = Trainable::class;

    protected static ?string $navigationIcon = 'heroicon-o-office-building';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (Closure $set, $state, $context) {
                        if ($context === 'create') {
                            $set('handle', Str::slug($state));
                        }
                    }),

                Forms\Components\TextInput::make('handle')->prefix('@')
                    ->required()
                    ->reactive()
                    ->debounce(1000)
                    ->afterStateUpdated(function (Closure $set, $state) {
                        $set('handle', Str::slug($state));
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\BadgeColumn::make('published_at')
                    ->color(fn($state) => $state ? 'success' : 'secondary')
                    ->formatStateUsing(fn($state) => $state ? 'Published' : 'Draft')
                    ->label('Published'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StatementsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'         => Pages\ListTrainables::route('/'),
            'create'        => Pages\CreateTrainable::route('/create'),
            'view'          => Pages\ViewTrainable::route('/{record}'),
            'edit'          => Pages\EditTrainable::route('/{record}/edit'),
            'bulk-training' => Pages\BulkTraining::route('/{record}/bulk-training'),
        ];
    }

    public static function getWidgets(): array
    {
        return [];
    }

    public static function getGlobalSearchResultUrl(Model $record): ?string
    {
        return route('filament.resources.properties.view', $record);
    }
}
