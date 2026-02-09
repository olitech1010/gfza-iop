<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MisTicketResource\Pages;
use App\Models\MisTicket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MisTicketResource extends Resource
{
    protected static ?string $model = MisTicket::class;

    protected static ?string $navigationGroup = 'MIS Support';

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    /**
     * Filter tickets based on user role.
     * Staff can only see their own tickets.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        // Staff can only see their own tickets
        if ($user->hasRole('staff') && ! $user->hasAnyRole(['super_admin', 'hr_manager', 'mis_support', 'dept_head'])) {
            return $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $isStaff = $user && $user->hasRole('staff') && ! $user->hasAnyRole(['super_admin', 'hr_manager', 'mis_support', 'dept_head']);

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
                    // Status - Staff cannot change, only MIS/Admin
                    Forms\Components\Select::make('status')
                        ->options([
                            'open' => 'Open',
                            'in_progress' => 'In Progress',
                            'resolved' => 'Resolved',
                            'closed' => 'Closed',
                        ])
                        ->default('open')
                        ->required()
                        ->disabled($isStaff)
                        ->dehydrated(! $isStaff),

                    // Requester - Staff cannot choose, auto-assigned
                    Forms\Components\Select::make('user_id')
                        ->relationship('requester', 'name')
                        ->label('Requester')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->default(fn () => $isStaff ? auth()->id() : null)
                        ->disabled($isStaff)
                        ->dehydrated(true),

                    // Assigned Agent - Staff cannot assign
                    Forms\Components\Select::make('assigned_to_user_id')
                        ->relationship('assignedStaff', 'name', fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'mis_support')))
                        ->label('Assigned Agent')
                        ->searchable()
                        ->preload()
                        ->visible(! $isStaff),

                    // Resolved At - Staff cannot set
                    Forms\Components\DateTimePicker::make('resolved_at')
                        ->visible(! $isStaff),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        $isStaff = $user && $user->hasRole('staff') && ! $user->hasAnyRole(['super_admin', 'hr_manager', 'mis_support', 'dept_head']);

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
                    ->sortable()
                    ->visible(! $isStaff),
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
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(! $isStaff),
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
