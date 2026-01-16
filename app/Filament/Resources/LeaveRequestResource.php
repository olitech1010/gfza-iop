<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveRequestResource\Pages;
use App\Filament\Resources\LeaveRequestResource\RelationManagers;
use App\Models\LeaveRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaveRequestResource extends Resource
{
    protected static ?string $navigationGroup = 'HR Operations';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Leave Requests';

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
                        ->disabled(fn () => !auth()->user()->job_title === 'HR'), // Only HR can change user, or maybe just purely auto
                    Forms\Components\DatePicker::make('start_date')
                        ->required()
                        ->native(false)
                        ->afterOrEqual('today')
                        ->rule(function (Forms\Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $start = \Carbon\Carbon::parse($value);
                                $user_id = $get('user_id') ?? auth()->id();
                                
                                // Check if user has leave in previous or next month
                                $consecutive = \App\Models\LeaveRequest::where('user_id', $user_id)
                                    ->where('status', '!=', 'rejected')
                                    ->where(function ($query) use ($start) {
                                        $prevMonth = $start->copy()->subMonth();
                                        $nextMonth = $start->copy()->addMonth();
                                        
                                        $query->whereMonth('start_date', $prevMonth->month)->whereYear('start_date', $prevMonth->year)
                                            ->orWhereMonth('end_date', $prevMonth->month)->whereYear('end_date', $prevMonth->year)
                                            ->orWhereMonth('start_date', $nextMonth->month)->whereYear('start_date', $nextMonth->year)
                                            ->orWhereMonth('end_date', $nextMonth->month)->whereYear('end_date', $nextMonth->year);
                                    })
                                    ->exists();

                                if ($consecutive) {
                                    $fail("You cannot take leave in consecutive months.");
                                }
                            };
                        }),
                    Forms\Components\DatePicker::make('end_date')
                        ->required()
                        ->native(false)
                        ->after('start_date')
                        ->rule(function (Forms\Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $start = \Carbon\Carbon::parse($get('start_date'));
                                $end = \Carbon\Carbon::parse($value);
                                $days = $start->diffInDays($end) + 1; // Inclusive

                                if ($days > 18) {
                                    $fail("You cannot request more than 18 days at a time.");
                                }
                            };
                        }),
                    Forms\Components\TextInput::make('days_requested')
                        ->numeric()
                        ->disabled()
                        ->dehydrated() // Ensure it saves
                        ->default(0), // Would ideally auto-calc via JS/Livewire lifecycle
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
                        ($record->status === 'pending_dept_head' && auth()->user()->id !== $record->user_id) || // Add role check logic here
                        ($record->status === 'pending_hr' && auth()->user()->department?->code === 'HR')
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
                    ->visible(fn (LeaveRequest $record) => in_array($record->status, ['pending_dept_head', 'pending_hr']))
                    ->action(function (LeaveRequest $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
