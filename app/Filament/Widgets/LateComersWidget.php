<?php

namespace App\Filament\Widgets;

use App\Models\Memo;
use App\Models\NssAttendance;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class LateComersWidget extends BaseWidget
{
    protected static ?string $heading = 'Late Comers This Week';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    /**
     * Prevent this widget from appearing on the dashboard.
     * It's only used as a header widget on the attendance page.
     */
    public static function canView(): bool
    {
        return false;
    }

    public ?string $weekStart = null;

    public function table(Table $table): Table
    {
        $start = $this->weekStart ? \Carbon\Carbon::parse($this->weekStart) : now();
        $startDate = $start->startOfWeek();
        $endDate = $start->copy()->endOfWeek();

        return $table
            ->query(
                NssAttendance::query()
                    ->select('user_id', DB::raw('COUNT(*) as late_count'))
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('status', 'late')
                    ->groupBy('user_id')
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('user.department.name')
                    ->label('Department'),
                TextColumn::make('late_count')
                    ->label('Late Days')
                    ->badge()
                    ->color('danger'),
                TextColumn::make('user.phone')
                    ->label('Phone'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('sendMemo')
                    ->label('Send Memo to All')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Send Memo to Late Comers')
                    ->modalDescription('This will create and publish a memo addressed to all staff who were late this week.')
                    ->action(function () use ($startDate, $endDate) {
                        $weekRange = $startDate->format('M d').' - '.$endDate->format('M d, Y');


                        $lateUserIds = NssAttendance::whereBetween('date', [$startDate, $endDate])
                            ->where('status', 'late')
                            ->distinct()
                            ->pluck('user_id')
                            ->toArray();

                        if (empty($lateUserIds)) {
                            Notification::make()
                                ->title('No late comers to notify')
                                ->warning()
                                ->send();

                            return;
                        }

                        $memo = Memo::create([
                            'title' => "Attendance Notice - Week of {$weekRange}",
                            'body' => "Dear Staff,\n\nYou have been identified as arriving late during the week of {$weekRange}.\n\nPlease report to the HR office for clarification at your earliest convenience.\n\nThank you for your cooperation.\n\nHR Department",
                            'status' => 'published',
                            'created_by' => auth()->id(),
                            'published_at' => now(),
                        ]);

                        $memo->recipients()->attach($lateUserIds);

                        Notification::make()
                            ->title('Memo sent successfully')
                            ->body('Memo sent to '.count($lateUserIds).' late comers.')
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('No late comers this week')
            ->emptyStateDescription('All staff arrived on time.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
