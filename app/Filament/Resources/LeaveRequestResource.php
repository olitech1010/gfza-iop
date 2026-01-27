<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveRequestResource\Pages;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaveRequestResource extends Resource
{
    protected static ?string $navigationGroup = 'HR Operations';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'ED Requests';

    protected static ?string $modelLabel = 'Excuse Duty Request';

    protected static ?string $pluralModelLabel = 'Excuse Duty Requests';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Request Details')->schema([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Staff Member')
                        ->default(fn () => auth()->id())
                        ->required()
                        ->searchable()
                        ->preload()
                        ->hidden(fn () => ! auth()->user()->hasAnyRole(['super_admin', 'hr_manager']))
                        ->dehydrated(),
                    Forms\Components\DatePicker::make('start_date')
                        ->required()
                        ->native(false)
                        ->afterOrEqual('today')
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::calculateDays($get, $set);
                        }),
                    Forms\Components\DatePicker::make('end_date')
                        ->required()
                        ->native(false)
                        ->afterOrEqual('start_date')
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::calculateDays($get, $set);
                        })
                        ->rule(function (Forms\Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $start = Carbon::parse($get('start_date'));
                                $end = Carbon::parse($value);
                                $days = $start->diffInDays($end) + 1; // Inclusive

                                if ($days > 18) {
                                    $fail('You cannot request more than 18 days at a time.');
                                }
                            };
                        }),
                    Forms\Components\TextInput::make('days_requested')
                        ->numeric()
                        ->disabled()
                        ->dehydrated()
                        ->default(0)
                        ->helperText('Auto-calculated from date range'),
                    Forms\Components\Textarea::make('reason')
                        ->required()
                        ->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('Status')->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'pending_dept_head' => 'Pending Dept Head',
                            'pending_hr' => 'Pending HR',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ])
                        ->default('pending_dept_head')
                        ->disabled(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Staff')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('days_requested')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => ['pending_dept_head', 'pending_hr'],
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn (LeaveRequest $record) =>
                        // Cannot approve own requests
                        $record->user_id !== auth()->id() &&
                        // Must be in approvable status and have correct role
                        (($record->status === 'pending_dept_head' && auth()->user()->hasAnyRole(['super_admin', 'dept_head'])) ||
                         ($record->status === 'pending_hr' && auth()->user()->hasAnyRole(['super_admin', 'hr_manager'])))
                    )
                    ->action(function (LeaveRequest $record) {
                        if ($record->status === 'pending_dept_head') {
                            $record->update([
                                'status' => 'pending_hr',
                                'dept_head_approved_at' => now(),
                                'dept_head_id' => auth()->id(),
                            ]);
                        } elseif ($record->status === 'pending_hr') {
                            $record->update([
                                'status' => 'approved',
                                'hr_approved_at' => now(),
                                'hr_id' => auth()->id(),
                            ]);
                        }
                    }),
                Tables\Actions\Action::make('reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')->required(),
                    ])
                    ->visible(fn (LeaveRequest $record) =>
                        // Cannot reject own requests
                        $record->user_id !== auth()->id() &&
                        // Must be in pending status and have correct role
                        in_array($record->status, ['pending_dept_head', 'pending_hr']) &&
                        auth()->user()->hasAnyRole(['super_admin', 'hr_manager', 'dept_head'])
                    )
                    ->action(function (LeaveRequest $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (LeaveRequest $record) =>
                        // Can only edit own pending requests
                        $record->user_id === auth()->id() && $record->status === 'pending_dept_head'
                    ),
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Staff and MIS support can only see their own leave requests
        if (auth()->user()->hasRole(['staff', 'mis_support'])) {
            return $query->where('user_id', auth()->id());
        }

        // HR, dept heads, and super admins see all leave requests
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }

    /**
     * Calculate days between start and end date (inclusive)
     */
    public static function calculateDays(Get $get, Set $set): void
    {
        $startDate = $get('start_date');
        $endDate = $get('end_date');

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            if ($end->gte($start)) {
                $days = $start->diffInDays($end) + 1; // +1 for inclusive
                $set('days_requested', $days);
            }
        }
    }
}
