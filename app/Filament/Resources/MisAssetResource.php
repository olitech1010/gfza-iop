<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MisAssetResource\Pages;
use App\Models\MisAsset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MisAssetResource extends Resource
{
    protected static ?string $model = MisAsset::class;

    protected static ?string $navigationGroup = 'MIS';

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Asset Identity')->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->label('Model Name')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('asset_tag')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('serial_number')
                        ->maxLength(255),
                    Forms\Components\Select::make('type')
                        ->options([
                            'Laptop' => 'Laptop',
                            'Desktop' => 'Desktop',
                            'Monitor' => 'Monitor',
                            'Printer' => 'Printer',
                            'Phone' => 'Phone',
                            'Accessory' => 'Accessory',
                        ])
                        ->required(),
                ])->columns(2),

                Forms\Components\Section::make('Status & Assignment')->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'active' => 'Active',
                            'repair' => 'In Repair',
                            'retired' => 'Retired',
                            'lost' => 'Lost/Stolen',
                        ])
                        ->default('active')
                        ->required(),
                    Forms\Components\Select::make('assigned_to_user_id')
                        ->relationship('assignedUser', 'name')
                        ->label('Assigned To')
                        ->searchable()
                        ->preload(),
                    Forms\Components\DatePicker::make('purchase_date'),
                ])->columns(3),

                Forms\Components\Section::make('Additional Info')->schema([
                    Forms\Components\Textarea::make('notes')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset_tag')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Model')
                    ->searchable()
                    ->description(fn (MisAsset $record): string => $record->serial_number ?? ''),
                Tables\Columns\TextColumn::make('type')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'repair',
                        'danger' => ['retired', 'lost'],
                    ]),
                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('Assigned To')
                    ->searchable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListMisAssets::route('/'),
            'create' => Pages\CreateMisAsset::route('/create'),
            'edit' => Pages\EditMisAsset::route('/{record}/edit'),
        ];
    }
}
