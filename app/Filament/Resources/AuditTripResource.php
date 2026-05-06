<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditTripResource\Pages;
use App\Models\AuditTrip;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditTripResource extends Resource
{
    protected static ?string $model = AuditTrip::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Transport & Logistics';

    protected static ?string $navigationLabel = 'Audit Schedules';

    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && $user->canAccessTransport();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Audit Trip Details')
                    ->schema([
                        Forms\Components\TextInput::make('team_name')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('e.g. TEAM 1'),
                        Forms\Components\Select::make('audit_type')
                            ->options([
                                'compliance' => 'Compliance',
                                'monitoring' => 'Monitoring',
                            ])
                            ->required(),
                        Forms\Components\Select::make('schedule_type')
                            ->options([
                                'internal' => 'Internal (Accra)',
                                'external' => 'External (Regional)',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('region')
                            ->maxLength(255)
                            ->placeholder('e.g. VOLTA 2, ASHANTI 1')
                            ->visible(fn (Forms\Get $get) => $get('schedule_type') === 'external'),
                        Forms\Components\TextInput::make('sequence_number')
                            ->numeric()
                            ->label('Sequence No.'),
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Company / Enterprise'),
                        Forms\Components\TextInput::make('scheduled_date')
                            ->required()
                            ->maxLength(255)
                            ->label('Scheduled Date(s)')
                            ->placeholder('e.g. 27th & 28th April, 2026'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'scheduled' => 'Scheduled',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'postponed' => 'Postponed',
                            ])
                            ->required()
                            ->default('scheduled'),
                    ])->columns(2),

                Forms\Components\Section::make('Team & Transport')
                    ->schema([
                        Forms\Components\Textarea::make('team_members')
                            ->required()
                            ->maxLength(65535)
                            ->label('Team Members')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('vehicle_id')
                            ->label('Assigned Vehicle')
                            ->relationship('vehicle', 'registration_number')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('driver_id')
                            ->label('Assigned Driver')
                            ->relationship('driver', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name ?? 'Unknown')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team_name')
                    ->label('Team')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('audit_type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'compliance' ? 'primary' : 'info')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('schedule_type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'internal' ? 'success' : 'warning')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('region')
                    ->searchable()
                    ->placeholder('Accra')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->limit(35),
                Tables\Columns\TextColumn::make('scheduled_date')
                    ->label('Date(s)')
                    ->limit(25),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'gray',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'postponed' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state))),
            ])
            ->defaultSort('team_name')
            ->filters([
                Tables\Filters\SelectFilter::make('team_name')
                    ->label('Team')
                    ->options(fn () => AuditTrip::distinct()->pluck('team_name', 'team_name')->toArray()),
                Tables\Filters\SelectFilter::make('audit_type')
                    ->options([
                        'compliance' => 'Compliance',
                        'monitoring' => 'Monitoring',
                    ]),
                Tables\Filters\SelectFilter::make('schedule_type')
                    ->options([
                        'internal' => 'Internal',
                        'external' => 'External',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'postponed' => 'Postponed',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_in_progress')
                    ->label('Start')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->visible(fn (AuditTrip $record): bool => $record->status === 'scheduled')
                    ->requiresConfirmation()
                    ->action(function (AuditTrip $record): void {
                        $record->update(['status' => 'in_progress']);
                        Notification::make()->success()->title('Audit marked as In Progress.')->send();
                    }),
                Tables\Actions\Action::make('mark_completed')
                    ->label('Complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (AuditTrip $record): bool => $record->status === 'in_progress')
                    ->requiresConfirmation()
                    ->action(function (AuditTrip $record): void {
                        $record->update(['status' => 'completed']);
                        Notification::make()->success()->title('Audit marked as Completed.')->send();
                    }),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditTrips::route('/'),
            'create' => Pages\CreateAuditTrip::route('/create'),
            'edit' => Pages\EditAuditTrip::route('/{record}/edit'),
        ];
    }
}
