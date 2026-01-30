<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Location;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Settings & Admin';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Info')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('name') ?? '') !== trim(($old ?? '').' '.($get('last_name') ?? ''))) {
                                    return;
                                }

                                $set('name', trim($state.' '.($get('last_name') ?? '')));
                            }),
                        Forms\Components\TextInput::make('middle_name'),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('name') ?? '') !== trim(($get('first_name') ?? '').' '.($old ?? ''))) {
                                    return;
                                }

                                $set('name', trim(($get('first_name') ?? '').' '.$state));
                            }),
                        Forms\Components\TextInput::make('name')
                            ->label('Display Name (Auto-filled)')
                            ->helperText('Will be auto-generated from First + Last if left empty')
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Employment Details')
                    ->schema([
                        Forms\Components\TextInput::make('staff_id')
                            ->label('Staff ID')
                            ->helperText('Auto-generated on creation')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('job_title'),
                        Forms\Components\Select::make('department_id')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(3),

                Forms\Components\Section::make('Contact & Location')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->regex('/^[0-9]{10}$/')
                            ->validationMessages([
                                'regex' => 'Phone number must be exactly 10 digits.',
                            ])
                            ->placeholder('0241234567'),
                        Forms\Components\Select::make('location_id')
                            ->relationship('location', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->unique(Location::class, 'name')
                                    ->label('Location Name'),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                return Location::create($data)->getKey();
                            }),
                    ])->columns(2),

                Forms\Components\Section::make('Account Security')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->helperText('Auto-generated from First + Last name on creation')
                            ->disabled(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                            ->dehydrated(fn ($livewire) => ! ($livewire instanceof Pages\CreateUser))
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                            ->dehydrated(fn ($state) => filled($state)),
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Assign Roles'),
                        Forms\Components\Toggle::make('is_active')->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('NSS Configuration')
                    ->schema([
                        Forms\Components\Toggle::make('is_nss')
                            ->label('NSS Personnel')
                            ->helperText('Check if this user is a National Service Person')
                            ->live(),
                        Forms\Components\FileUpload::make('photo')
                            ->label('ID Photo')
                            ->image()
                            ->imageEditor()
                            ->directory('user-photos')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('Photo for NSS ID card (max 2MB)')
                            ->visible(fn (Get $get): bool => $get('is_nss')),
                        Forms\Components\TextInput::make('nss_pin')
                            ->label('4-Digit PIN')
                            ->password()
                            ->revealable()
                            ->maxLength(4)
                            ->minLength(4)
                            ->numeric()
                            ->helperText('For kiosk check-in (leave blank to keep existing)')
                            ->dehydrated(fn ($state) => filled($state))
                            ->visible(fn (Get $get): bool => $get('is_nss')),
                        Forms\Components\Placeholder::make('qr_token_status')
                            ->label('QR Token')
                            ->content(fn (?User $record): string => $record?->qr_token ? 'Generated ✓' : 'Not generated')
                            ->visible(fn (Get $get): bool => $get('is_nss')),
                    ])->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('staff_id')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Full Name')->searchable(['first_name', 'last_name', 'name']),
                Tables\Columns\TextColumn::make('job_title')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('department.name')->sortable(),
                Tables\Columns\TextColumn::make('phone')->label('Phone')->searchable(),
                Tables\Columns\TextColumn::make('location.name')->label('Location')->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(','),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('showIdCard')
                    ->label('Show ID')
                    ->icon('heroicon-o-identification')
                    ->color('success')
                    ->visible(fn (User $record): bool => $record->is_nss)
                    ->modalHeading('NSS ID Card')
                    ->modalWidth('md')
                    ->modalContent(function (User $record) {
                        // Generate QR token if not exists
                        if (! $record->qr_token) {
                            $record->generateQrToken();
                        }

                        return view('filament.modals.nss-id-card', ['user' => $record]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                Tables\Actions\Action::make('enrollFace')
                    ->label('Enroll Face')
                    ->icon('heroicon-o-face-smile')
                    ->color('indigo')
                    ->visible(fn (User $record): bool => $record->is_nss)
                    ->modalHeading('Enroll Face Recognition')
                    ->modalContent(fn (User $record) => view('filament.modals.face-enrollment-wrapper', ['user' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Done'),
                Tables\Actions\Action::make('generateQr')
                    ->label('New QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->visible(fn (User $record): bool => $record->is_nss)
                    ->requiresConfirmation()
                    ->modalDescription('This will invalidate the existing QR code on the user\'s ID card.')
                    ->action(function (User $record) {
                        $record->generateQrToken();
                    }),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
