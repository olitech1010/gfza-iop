<?php

namespace App\Filament\Resources\NssAttendanceResource\Pages;

use App\Filament\Resources\NssAttendanceResource;
use App\Filament\Widgets\AttendanceChartsWidget;
use App\Filament\Widgets\AttendanceStatsWidget;
use App\Filament\Widgets\LateComersWidget;
use App\Filament\Widgets\NssAttendanceSummaryWidget;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\Url;

class ListNssAttendances extends ListRecords
{
    protected static string $resource = NssAttendanceResource::class;

    #[Url]
    public ?string $weekStart = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('selectWeek')
                ->label('Filter Stats Week')
                ->icon('heroicon-o-calendar')
                ->form([
                    Forms\Components\DatePicker::make('weekStart')
                        ->label('Select Week')
                        ->default(now()->startOfWeek())
                        ->native(false)
                        ->displayFormat('M d, Y')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->weekStart = \Carbon\Carbon::parse($data['weekStart'])->startOfWeek()->format('Y-m-d');
                    $this->redirect(static::getResource()::getUrl('index', ['weekStart' => $this->weekStart]));
                }),
        ];
    }

    protected function getHeaderWidgetsData(): array
    {
        return [
            'weekStart' => $this->weekStart,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getHeaderWidgets(): array
    {
        $user = auth()->user();

        // Only show full reports to HR and super admin
        if ($user && ($user->hasRole('super_admin') || $user->hasRole('hr_manager'))) {
            return [
                AttendanceStatsWidget::class,
                AttendanceChartsWidget::class,
                LateComersWidget::class,
            ];
        }

        // Show basic stats for dept heads
        if ($user && $user->hasRole('dept_head')) {
            return [
                AttendanceStatsWidget::class,
            ];
        }

        return [];
    }
}
