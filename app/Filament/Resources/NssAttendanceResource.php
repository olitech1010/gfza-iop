<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NssAttendanceResource\Pages;
use App\Models\NssAttendance;
use App\Settings\AttendanceSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NssAttendanceResource extends Resource
{
    protected static ?string $model = NssAttendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'HR Operations';

    protected static ?string $navigationLabel = 'NSS Attendance';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Attendance Record')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name', fn ($query) => $query->where('is_nss', true))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('NSS Personnel'),
                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->default(today()),
                        Forms\Components\TimePicker::make('check_in_time')
                            ->label('Check-in Time')
                            ->seconds(false),
                        Forms\Components\TimePicker::make('check_out_time')
                            ->label('Check-out Time')
                            ->seconds(false),
                        Forms\Components\Select::make('status')
                            ->options([
                                'present' => 'Present (On Time)',
                                'late' => 'Late',
                                'absent' => 'Absent',
                            ])
                            ->required()
                            ->default('present'),
                        Forms\Components\Select::make('check_in_method')
                            ->options([
                                'qr_code' => 'QR Code',
                                'pin' => 'PIN',
                                'manual' => 'Manual Entry',
                            ]),
                        Forms\Components\Textarea::make('notes')
                            ->rows(2),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $settings = app(AttendanceSettings::class);

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('NSS Personnel')
                    ->searchable(['first_name', 'last_name', 'name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.department.name')
                    ->label('Department')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('check_in_time')
                    ->label('Check In')
                    ->time('h:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_out_time')
                    ->label('Check Out')
                    ->time('h:i A')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('working_hours')
                    ->label('Hours')
                    ->formatStateUsing(fn ($state) => $state ? $state.'h' : '-'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'present',
                        'warning' => 'late',
                        'danger' => 'absent',
                    ]),
                Tables\Columns\TextColumn::make('check_in_method')
                    ->label('Method')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'present' => 'Present',
                        'late' => 'Late',
                        'absent' => 'Absent',
                    ]),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('date', today()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('markCheckout')
                    ->label('Check Out')
                    ->icon('heroicon-o-arrow-right-end-on-rectangle')
                    ->color('warning')
                    ->visible(fn (NssAttendance $record): bool => $record->check_in_time && ! $record->check_out_time)
                    ->action(function (NssAttendance $record) {
                        $record->update([
                            'check_out_time' => now()->toTimeString(),
                            'check_out_method' => 'manual',
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNssAttendances::route('/'),
            'create' => Pages\CreateNssAttendance::route('/create'),
            'edit' => Pages\EditNssAttendance::route('/{record}/edit'),
        ];
    }
}
