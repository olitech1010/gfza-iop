<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DriverTripReviewResource\Pages;
use App\Models\Driver;
use App\Models\DriverTripReview;
use App\Models\Vehicle;
use App\Models\VehicleRequisition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DriverTripReviewResource extends Resource
{
    protected static ?string $model = DriverTripReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Transport & Logistics';

    protected static ?string $navigationLabel = 'Driver Reviews';

    protected static ?int $navigationSort = 7;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && $user->canAccessTransport();
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();

        return $user && $user->hasRole('super_admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Trip Information')
                    ->description('Select the driver, vehicle, and trip being reviewed.')
                    ->schema([
                        Forms\Components\Select::make('review_type')
                            ->label('Review Type')
                            ->options([
                                'admin' => 'Admin Review (Vehicle & Technical)',
                                'passenger' => 'Passenger Review (Ride Experience)',
                            ])
                            ->required()
                            ->live()
                            ->native(false)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('driver_id')
                            ->label('Driver')
                            ->options(
                                Driver::where('status', 'active')
                                    ->with('user')
                                    ->get()
                                    ->mapWithKeys(fn (Driver $d) => [$d->id => $d->user->name." ({$d->license_number})"])
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->options(
                                Vehicle::all()
                                    ->mapWithKeys(fn (Vehicle $v) => [
                                        $v->id => "{$v->make} {$v->model} ({$v->registration_number}) — ".ucfirst($v->transmission),
                                    ])
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $vehicle = Vehicle::find($state);
                                    $set('transmission_used', $vehicle?->transmission ?? 'manual');
                                }
                            }),

                        Forms\Components\Select::make('transmission_used')
                            ->label('Transmission Used')
                            ->options([
                                'manual' => 'Manual',
                                'automatic' => 'Automatic',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('vehicle_requisition_id')
                            ->label('Linked Trip (Requisition)')
                            ->options(function (Get $get) {
                                $driverId = $get('driver_id');
                                if (! $driverId) {
                                    return [];
                                }

                                return VehicleRequisition::where('driver_id', $driverId)
                                    ->where('status', 'completed')
                                    ->get()
                                    ->mapWithKeys(fn (VehicleRequisition $r) => [
                                        $r->id => "{$r->reference_number} — {$r->destination} ({$r->requested_date->format('d M Y')})",
                                    ]);
                            })
                            ->searchable()
                            ->preload()
                            ->helperText('Optional. Link this review to a specific completed trip.'),

                        Forms\Components\DatePicker::make('review_date')
                            ->label('Review Date')
                            ->required()
                            ->default(now()),

                        Forms\Components\Hidden::make('reviewed_by')
                            ->default(auth()->id()),
                    ])->columns(2),

                // Admin Ratings
                Forms\Components\Section::make('Vehicle & Technical Assessment')
                    ->description('Rate the driver\'s handling of the vehicle and compliance with rules.')
                    ->visible(fn (Get $get): bool => $get('review_type') === 'admin')
                    ->schema([
                        Forms\Components\Radio::make('vehicle_condition')
                            ->label('Vehicle Condition')
                            ->helperText(DriverTripReview::ADMIN_RATING_LABELS['vehicle_condition'])
                            ->options([1 => '★ Poor', 2 => '★★ Below Average', 3 => '★★★ Acceptable', 4 => '★★★★ Good', 5 => '★★★★★ Excellent'])
                            ->required()
                            ->inline(),
                        Forms\Components\Radio::make('cleanliness')
                            ->label('Cleanliness')
                            ->helperText(DriverTripReview::ADMIN_RATING_LABELS['cleanliness'])
                            ->options([1 => '★ Poor', 2 => '★★ Below Average', 3 => '★★★ Acceptable', 4 => '★★★★ Good', 5 => '★★★★★ Excellent'])
                            ->required()
                            ->inline(),
                        Forms\Components\Radio::make('fuel_efficiency')
                            ->label('Fuel Efficiency')
                            ->helperText(DriverTripReview::ADMIN_RATING_LABELS['fuel_efficiency'])
                            ->options([1 => '★ Poor', 2 => '★★ Below Average', 3 => '★★★ Acceptable', 4 => '★★★★ Good', 5 => '★★★★★ Excellent'])
                            ->required()
                            ->inline(),
                        Forms\Components\Radio::make('timeliness')
                            ->label('Timeliness')
                            ->helperText(DriverTripReview::ADMIN_RATING_LABELS['timeliness'])
                            ->options([1 => '★ Poor', 2 => '★★ Below Average', 3 => '★★★ Acceptable', 4 => '★★★★ Good', 5 => '★★★★★ Excellent'])
                            ->required()
                            ->inline(),
                        Forms\Components\Radio::make('rule_compliance')
                            ->label('Rule Compliance')
                            ->helperText(DriverTripReview::ADMIN_RATING_LABELS['rule_compliance'])
                            ->options([1 => '★ Poor', 2 => '★★ Below Average', 3 => '★★★ Acceptable', 4 => '★★★★ Good', 5 => '★★★★★ Excellent'])
                            ->required()
                            ->inline(),
                    ]),

                // Passenger Ratings
                Forms\Components\Section::make('Ride Experience')
                    ->description('Rate your experience with this driver during the trip.')
                    ->visible(fn (Get $get): bool => $get('review_type') === 'passenger')
                    ->schema([
                        Forms\Components\Radio::make('punctuality')
                            ->label('Punctuality')
                            ->helperText(DriverTripReview::PASSENGER_RATING_LABELS['punctuality'])
                            ->options([1 => '★ Poor', 2 => '★★ Below Average', 3 => '★★★ Acceptable', 4 => '★★★★ Good', 5 => '★★★★★ Excellent'])
                            ->required()
                            ->inline(),
                        Forms\Components\Radio::make('driving_quality')
                            ->label('Driving Quality')
                            ->helperText(DriverTripReview::PASSENGER_RATING_LABELS['driving_quality'])
                            ->options([1 => '★ Poor', 2 => '★★ Below Average', 3 => '★★★ Acceptable', 4 => '★★★★ Good', 5 => '★★★★★ Excellent'])
                            ->required()
                            ->inline(),
                        Forms\Components\Radio::make('professionalism')
                            ->label('Professionalism')
                            ->helperText(DriverTripReview::PASSENGER_RATING_LABELS['professionalism'])
                            ->options([1 => '★ Poor', 2 => '★★ Below Average', 3 => '★★★ Acceptable', 4 => '★★★★ Good', 5 => '★★★★★ Excellent'])
                            ->required()
                            ->inline(),
                        Forms\Components\Radio::make('safety_feeling')
                            ->label('Safety')
                            ->helperText(DriverTripReview::PASSENGER_RATING_LABELS['safety_feeling'])
                            ->options([1 => '★ Poor', 2 => '★★ Below Average', 3 => '★★★ Acceptable', 4 => '★★★★ Good', 5 => '★★★★★ Excellent'])
                            ->required()
                            ->inline(),
                        Forms\Components\Radio::make('overall_satisfaction')
                            ->label('Overall Satisfaction')
                            ->helperText(DriverTripReview::PASSENGER_RATING_LABELS['overall_satisfaction'])
                            ->options([1 => '★ Poor', 2 => '★★ Below Average', 3 => '★★★ Acceptable', 4 => '★★★★ Good', 5 => '★★★★★ Excellent'])
                            ->required()
                            ->inline(),
                    ]),

                // Admin Qualitative
                Forms\Components\Section::make('Vehicle Inspection Details')
                    ->description('Document any damage, incidents, or mechanical issues found.')
                    ->visible(fn (Get $get): bool => $get('review_type') === 'admin')
                    ->schema([
                        Forms\Components\Select::make('damage_severity')
                            ->label('Damage Severity')
                            ->options([
                                'none' => '✅ None — No damage found',
                                'minor' => '🟡 Minor — Small scratches, scuffs',
                                'moderate' => '🟠 Moderate — Noticeable dents, cracked lights',
                                'severe' => '🔴 Severe — Major structural or mechanical damage',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Textarea::make('damage_notes')
                            ->label('Damage Details')
                            ->helperText('Describe any damage found on the vehicle upon return.')
                            ->rows(3),
                        Forms\Components\Textarea::make('incidents')
                            ->label('Incidents Reported')
                            ->helperText('Any accidents, near-misses, traffic violations, or safety concerns.')
                            ->rows(3),
                        Forms\Components\Textarea::make('mechanical_issues')
                            ->label('Mechanical Issues')
                            ->helperText('Any mechanical problems discovered on return (brakes, engine, transmission, etc.).')
                            ->rows(3),
                        Forms\Components\Select::make('recommendation')
                            ->label('Driver Recommendation')
                            ->options([
                                'recommended' => '✅ Recommended — Continue assigning trips',
                                'needs_training' => '⚠️ Needs Training — Assign with caution, schedule training',
                                'restricted' => '🟠 Restricted — Limit to certain vehicle types only',
                                'not_recommended' => '🔴 Not Recommended — Suspend from driving duties',
                            ])
                            ->required()
                            ->native(false),
                    ]),

                // Passenger Qualitative
                Forms\Components\Section::make('Additional Feedback')
                    ->visible(fn (Get $get): bool => $get('review_type') === 'passenger')
                    ->schema([
                        Forms\Components\Textarea::make('compliments')
                            ->label('What did the driver do well?')
                            ->helperText('Share any positive experiences or things the driver excelled at.')
                            ->rows(3),
                        Forms\Components\Textarea::make('complaints')
                            ->label('Any issues or concerns?')
                            ->helperText('Describe any problems experienced during the trip.')
                            ->rows(3),
                    ]),

                // Shared
                Forms\Components\Section::make('General Comments')
                    ->schema([
                        Forms\Components\Textarea::make('comments')
                            ->label('Additional Comments')
                            ->helperText('Any other observations or notes about this trip.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('driver.user.name')
                    ->label('Driver')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vehicle.registration_number')
                    ->label('Vehicle')
                    ->searchable(),
                Tables\Columns\TextColumn::make('review_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'info',
                        'passenger' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('transmission_used')
                    ->label('Trans.')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => $state === 'manual' ? 'M' : 'A'),
                Tables\Columns\TextColumn::make('overall_rating')
                    ->label('Rating')
                    ->formatStateUsing(function ($state) {
                        if (! $state) {
                            return '—';
                        }
                        $stars = str_repeat('★', (int) round($state));
                        $empty = str_repeat('☆', 5 - (int) round($state));

                        return $stars.$empty.' '.number_format($state, 1);
                    })
                    ->sortable()
                    ->color(fn ($state) => match (true) {
                        $state >= 4.0 => 'success',
                        $state >= 3.0 => 'warning',
                        $state > 0 => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('recommendation')
                    ->label('Rec.')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'recommended' => 'success',
                        'needs_training' => 'warning',
                        'restricted' => 'danger',
                        'not_recommended' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'recommended' => 'Recommended',
                        'needs_training' => 'Needs Training',
                        'restricted' => 'Restricted',
                        'not_recommended' => 'Not Recommended',
                        default => '—',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('review_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label('Reviewed By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('driver_id')
                    ->relationship('driver.user', 'name')
                    ->label('Driver')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('review_type')
                    ->options([
                        'admin' => 'Admin Review',
                        'passenger' => 'Passenger Review',
                    ]),
                Tables\Filters\SelectFilter::make('recommendation')
                    ->options([
                        'recommended' => 'Recommended',
                        'needs_training' => 'Needs Training',
                        'restricted' => 'Restricted',
                        'not_recommended' => 'Not Recommended',
                    ]),
                Tables\Filters\SelectFilter::make('transmission_used')
                    ->options([
                        'manual' => 'Manual',
                        'automatic' => 'Automatic',
                    ])
                    ->label('Transmission'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Trip Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('driver.user.name')->label('Driver'),
                        Infolists\Components\TextEntry::make('vehicle.registration_number')->label('Vehicle'),
                        Infolists\Components\TextEntry::make('transmission_used')
                            ->label('Transmission')
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        Infolists\Components\TextEntry::make('review_type')
                            ->label('Review Type')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        Infolists\Components\TextEntry::make('vehicleRequisition.reference_number')
                            ->label('Trip Reference')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('review_date')->label('Review Date')->date(),
                        Infolists\Components\TextEntry::make('reviewer.name')->label('Reviewed By'),
                        Infolists\Components\TextEntry::make('overall_rating')
                            ->label('Overall Rating')
                            ->formatStateUsing(function ($state) {
                                $stars = str_repeat('★', (int) round($state));
                                $empty = str_repeat('☆', 5 - (int) round($state));

                                return $stars.$empty.' ('.number_format($state, 2).'/5.00)';
                            })
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                    ])->columns(3),

                Infolists\Components\Section::make('Ratings')
                    ->schema([
                        // Admin ratings
                        Infolists\Components\TextEntry::make('vehicle_condition')->label('Vehicle Condition')->visible(fn ($record) => $record->review_type === 'admin')->formatStateUsing(fn ($state) => $state ? str_repeat('★', $state).str_repeat('☆', 5 - $state) : '—'),
                        Infolists\Components\TextEntry::make('cleanliness')->label('Cleanliness')->visible(fn ($record) => $record->review_type === 'admin')->formatStateUsing(fn ($state) => $state ? str_repeat('★', $state).str_repeat('☆', 5 - $state) : '—'),
                        Infolists\Components\TextEntry::make('fuel_efficiency')->label('Fuel Efficiency')->visible(fn ($record) => $record->review_type === 'admin')->formatStateUsing(fn ($state) => $state ? str_repeat('★', $state).str_repeat('☆', 5 - $state) : '—'),
                        Infolists\Components\TextEntry::make('timeliness')->label('Timeliness')->visible(fn ($record) => $record->review_type === 'admin')->formatStateUsing(fn ($state) => $state ? str_repeat('★', $state).str_repeat('☆', 5 - $state) : '—'),
                        Infolists\Components\TextEntry::make('rule_compliance')->label('Rule Compliance')->visible(fn ($record) => $record->review_type === 'admin')->formatStateUsing(fn ($state) => $state ? str_repeat('★', $state).str_repeat('☆', 5 - $state) : '—'),
                        // Passenger ratings
                        Infolists\Components\TextEntry::make('punctuality')->label('Punctuality')->visible(fn ($record) => $record->review_type === 'passenger')->formatStateUsing(fn ($state) => $state ? str_repeat('★', $state).str_repeat('☆', 5 - $state) : '—'),
                        Infolists\Components\TextEntry::make('driving_quality')->label('Driving Quality')->visible(fn ($record) => $record->review_type === 'passenger')->formatStateUsing(fn ($state) => $state ? str_repeat('★', $state).str_repeat('☆', 5 - $state) : '—'),
                        Infolists\Components\TextEntry::make('professionalism')->label('Professionalism')->visible(fn ($record) => $record->review_type === 'passenger')->formatStateUsing(fn ($state) => $state ? str_repeat('★', $state).str_repeat('☆', 5 - $state) : '—'),
                        Infolists\Components\TextEntry::make('safety_feeling')->label('Safety')->visible(fn ($record) => $record->review_type === 'passenger')->formatStateUsing(fn ($state) => $state ? str_repeat('★', $state).str_repeat('☆', 5 - $state) : '—'),
                        Infolists\Components\TextEntry::make('overall_satisfaction')->label('Overall Satisfaction')->visible(fn ($record) => $record->review_type === 'passenger')->formatStateUsing(fn ($state) => $state ? str_repeat('★', $state).str_repeat('☆', 5 - $state) : '—'),
                    ])->columns(5),

                Infolists\Components\Section::make('Detailed Assessment')
                    ->schema([
                        Infolists\Components\TextEntry::make('damage_severity')
                            ->label('Damage Severity')
                            ->visible(fn ($record) => $record->review_type === 'admin')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'none' => 'success',
                                'minor' => 'warning',
                                'moderate' => 'danger',
                                'severe' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? 'N/A')),
                        Infolists\Components\TextEntry::make('recommendation')
                            ->label('Recommendation')
                            ->visible(fn ($record) => $record->review_type === 'admin')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'recommended' => 'success',
                                'needs_training' => 'warning',
                                'restricted' => 'danger',
                                'not_recommended' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (?string $state): string => str_replace('_', ' ', ucfirst($state ?? 'N/A'))),
                        Infolists\Components\TextEntry::make('damage_notes')->label('Damage Notes')->visible(fn ($record) => $record->review_type === 'admin' && $record->damage_notes)->columnSpanFull(),
                        Infolists\Components\TextEntry::make('incidents')->label('Incidents')->visible(fn ($record) => $record->review_type === 'admin' && $record->incidents)->columnSpanFull(),
                        Infolists\Components\TextEntry::make('mechanical_issues')->label('Mechanical Issues')->visible(fn ($record) => $record->review_type === 'admin' && $record->mechanical_issues)->columnSpanFull(),
                        Infolists\Components\TextEntry::make('compliments')->label('Compliments')->visible(fn ($record) => $record->review_type === 'passenger' && $record->compliments)->columnSpanFull(),
                        Infolists\Components\TextEntry::make('complaints')->label('Complaints')->visible(fn ($record) => $record->review_type === 'passenger' && $record->complaints)->columnSpanFull(),
                        Infolists\Components\TextEntry::make('comments')->label('Comments')->visible(fn ($record) => $record->comments)->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDriverTripReviews::route('/'),
            'create' => Pages\CreateDriverTripReview::route('/create'),
            'view' => Pages\ViewDriverTripReview::route('/{record}'),
        ];
    }
}
