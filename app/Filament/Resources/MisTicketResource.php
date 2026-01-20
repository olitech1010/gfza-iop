<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MisTicketResource\Pages;
use App\Models\MisTicket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MisTicketResource extends Resource
{
    protected static ?string $model = MisTicket::class;

    protected static ?string $navigationGroup = 'MIS Support';

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ticket Information')->schema([
                    Forms\Components\TextInput::make('subject')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),
                    Forms\Components\Select::make('category')
                        ->options([
                            'hardware' => 'Hardware',
                            'software' => 'Software',
                            'network' => 'Network',
                            'other' => 'Other',
                        ])
                        ->required(),
                    Forms\Components\Select::make('priority')
                        ->options([
                            'low' => 'Low',
                            'medium' => 'Medium',
                            'high' => 'High',
                            'critical' => 'Critical',
                        ])
                        ->default('medium')
                        ->required(),
                ])->columns(2),

                Forms\Components\Section::make('Status & Assignment')->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'open' => 'Open',
                            'in_progress' => 'In Progress',
                            'resolved' => 'Resolved',
                            'closed' => 'Closed',
                        ])
                        ->default('open')
                        ->required(),
                    Forms\Components\Select::make('user_id')
                        ->relationship('requester', 'name')
                        ->label('Requester')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('assigned_to_user_id')
                        ->relationship('assignedStaff', 'name')
                        ->label('Assigned Agent')
                        ->searchable()
                        ->preload(),
                    Forms\Components\DateTimePicker::make('resolved_at'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(50)
                    ->description(fn (MisTicket $record): string => \Illuminate\Support\Str::limit($record->description, 50)),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'open',
                        'warning' => 'in_progress',
                        'success' => 'resolved',
                        'gray' => 'closed',
                    ]),
                Tables\Columns\BadgeColumn::make('priority')
                    ->colors([
                        'success' => 'low',
                        'info' => 'medium',
                        'warning' => 'high',
                        'danger' => 'critical',
                    ]),
                Tables\Columns\TextColumn::make('category'),
                Tables\Columns\TextColumn::make('requester.name')
                    ->label('Requester')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedStaff.name')
                    ->label('Agent')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
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
            'index' => Pages\ListMisTickets::route('/'),
            'create' => Pages\CreateMisTicket::route('/create'),
            'edit' => Pages\EditMisTicket::route('/{record}/edit'),
        ];
    }
}
