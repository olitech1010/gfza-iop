<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DriverResource\Pages;
use App\Models\Driver;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Transport & Logistics';

    protected static ?string $navigationLabel = 'Drivers';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && $user->canAccessTransport();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Driver Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Staff Member')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Select the staff member who is a driver.'),
                        Forms\Components\TextInput::make('license_number')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. DL-1234567'),
                        Forms\Components\DatePicker::make('license_expiry')
                            ->label('License Expiry Date'),
                        Forms\Components\Select::make('license_class')
                            ->options([
                                'B' => 'Class B - Light Vehicles',
                                'C' => 'Class C - Heavy Vehicles',
                                'D' => 'Class D - Buses',
                                'E' => 'Class E - Special',
                            ])
                            ->placeholder('Select license class'),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'on_leave' => 'On Leave',
                            ])
                            ->required()
                            ->default('active'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Driver Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('license_number')
                    ->label('License No.')
                    ->searchable(),
                Tables\Columns\TextColumn::make('license_class')
                    ->label('Class')
                    ->badge(),
                Tables\Columns\TextColumn::make('license_expiry')
                    ->label('License Expiry')
                    ->date()
                    ->color(fn (Driver $record): string => $record->license_expiry && $record->license_expiry->isPast() ? 'danger' : ($record->license_expiry && $record->license_expiry->diffInDays(now()) < 30 ? 'warning' : 'gray')),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'on_leave' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state))),
                Tables\Columns\TextColumn::make('requisitions_count')
                    ->label('Trips')
                    ->counts('requisitions')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'on_leave' => 'On Leave',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
