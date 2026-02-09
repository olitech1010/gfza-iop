<?php

namespace App\Filament\Pages;

use App\Settings\AttendanceSettings;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageAttendanceSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings & Admin';

    protected static ?string $navigationLabel = 'Attendance Settings';

    protected static ?string $title = 'Attendance Settings';

    protected static ?int $navigationSort = 99;

    /**
     * Only admins can access attendance settings.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && $user->hasAnyRole(['super_admin', 'hr_manager']);
    }

    protected static string $view = 'filament.pages.manage-attendance-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = app(AttendanceSettings::class);

        $this->form->fill([
            'expected_arrival' => $settings->expected_arrival,
            'expected_departure' => $settings->expected_departure,
            'grace_period_minutes' => $settings->grace_period_minutes,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Working Hours')
                    ->description('Configure the expected working hours for NSS personnel.')
                    ->schema([
                        Forms\Components\TextInput::make('expected_arrival')
                            ->label('Expected Arrival Time')
                            ->helperText('Staff arriving after this time will be marked as late (HH:MM format)')
                            ->placeholder('08:30')
                            ->required(),
                        Forms\Components\TextInput::make('expected_departure')
                            ->label('Expected Departure Time')
                            ->helperText('Expected closing time for the day (HH:MM format)')
                            ->placeholder('17:00')
                            ->required(),
                        Forms\Components\TextInput::make('grace_period_minutes')
                            ->label('Grace Period (Minutes)')
                            ->helperText('Extra minutes allowed before marking as late (0 for none)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(60)
                            ->default(0)
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Information')
                    ->schema([
                        Forms\Components\Placeholder::make('kiosk_url')
                            ->label('Kiosk URL')
                            ->content(fn () => url('/kiosk'))
                            ->helperText('Use this URL on the kiosk device'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = app(AttendanceSettings::class);
        $settings->expected_arrival = $data['expected_arrival'];
        $settings->expected_departure = $data['expected_departure'];
        $settings->grace_period_minutes = (int) $data['grace_period_minutes'];
        $settings->save();

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
