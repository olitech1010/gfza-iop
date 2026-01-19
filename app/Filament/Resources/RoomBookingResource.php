<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomBookingResource\Pages;
use App\Models\ConferenceRoom;
use App\Models\RoomBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RoomBookingResource extends Resource
{
    protected static ?string $model = RoomBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Facilities';

    protected static ?string $modelLabel = 'Room Booking';

    protected static ?string $pluralModelLabel = 'Room Bookings';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Booking Details')
                    ->description('Schedule your conference room booking')
                    ->schema([
                        Forms\Components\Select::make('conference_room_id')
                            ->label('Conference Room')
                            ->relationship('conferenceRoom', 'name')
                            ->default(fn () => ConferenceRoom::first()?->id)
                            ->required()
                            ->preload()
                            ->searchable()
                            ->native(false),

                        Forms\Components\Select::make('user_id')
                            ->label('Booked By')
                            ->relationship('user', 'name')
                            ->default(fn () => auth()->id())
                            ->required()
                            ->preload()
                            ->searchable()
                            ->native(false),

                        Forms\Components\TextInput::make('title')
                            ->label('Meeting Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Weekly Team Standup')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Description / Agenda')
                            ->placeholder('Optional: Add meeting agenda or notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Schedule')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_time')
                            ->label('Start Time')
                            ->required()
                            ->native(false)
                            ->minDate(now())
                            ->minutesStep(15)
                            ->seconds(false)
                            ->displayFormat('D, M j, Y g:i A')
                            ->reactive(),

                        Forms\Components\DateTimePicker::make('end_time')
                            ->label('End Time')
                            ->required()
                            ->native(false)
                            ->minDate(fn (Get $get) => $get('start_time'))
                            ->minutesStep(15)
                            ->seconds(false)
                            ->displayFormat('D, M j, Y g:i A')
                            ->after('start_time')
                            ->rules([
                                fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $roomId = $get('conference_room_id');
                                    $startTime = $get('start_time');
                                    $recordId = request()->route('record');

                                    if (! $roomId || ! $startTime || ! $value) {
                                        return;
                                    }

                                    $hasConflict = RoomBooking::overlapping(
                                        $roomId,
                                        $startTime,
                                        $value,
                                        $recordId
                                    )->exists();

                                    if ($hasConflict) {
                                        $fail('This time slot conflicts with an existing booking. Please choose a different time.');
                                    }
                                },
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'confirmed' => 'Confirmed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('confirmed')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(1)
                    ->visible(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Meeting')
                    ->searchable()
                    ->weight('bold')
                    ->limit(30),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Booked By')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start')
                    ->dateTime('D, M j, g:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('End')
                    ->dateTime('g:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->getStateUsing(fn (RoomBooking $record): string => $record->duration),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'confirmed',
                        'danger' => 'cancelled',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('start_time', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\Filter::make('upcoming')
                    ->label('Upcoming Only')
                    ->query(fn (Builder $query): Builder => $query->where('start_time', '>=', now()))
                    ->default(),

                Tables\Filters\Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('start_time', today())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Booking')
                    ->modalDescription('Are you sure you want to cancel this booking? This action cannot be undone.')
                    ->visible(fn (RoomBooking $record): bool => $record->status === 'confirmed')
                    ->action(fn (RoomBooking $record) => $record->update(['status' => 'cancelled'])),
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
            'index' => Pages\ListRoomBookings::route('/'),
            'create' => Pages\CreateRoomBooking::route('/create'),
            'edit' => Pages\EditRoomBooking::route('/{record}/edit'),
        ];
    }
}
