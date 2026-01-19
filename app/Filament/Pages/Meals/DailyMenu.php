<?php

namespace App\Filament\Pages\Meals;

use App\Models\Department;
use App\Models\MealRequest;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class DailyMenu extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Meal Management';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Daily Menu & Serving';

    protected static string $view = 'filament.pages.meals.daily-menu';

    public ?string $selectedDate = null;

    public ?string $selectedDepartment = null;

    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('user.name')
                    ->label('Staff Member')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('user.department.name')
                    ->label('Department')
                    ->sortable(),

                TextColumn::make('weeklyMenuItem.mealItem.name')
                    ->label('Meal')
                    ->limit(40),

                IconColumn::make('is_nss')
                    ->label('NSS')
                    ->boolean()
                    ->trueIcon('heroicon-o-academic-cap')
                    ->falseIcon('heroicon-o-minus'),

                TextColumn::make('amount_due')
                    ->label('Amount')
                    ->money('GHS'),

                IconColumn::make('is_paid')
                    ->label('Paid')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                IconColumn::make('is_served')
                    ->label('Served')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('warning'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('mark_paid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->visible(fn (MealRequest $record): bool => ! $record->is_paid && ! $record->is_nss)
                    ->action(fn (MealRequest $record) => $record->markAsPaid()),

                Action::make('mark_served')
                    ->label('Mark Served')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (MealRequest $record): bool => ! $record->is_served)
                    ->action(fn (MealRequest $record) => $record->markAsServed()),
            ])
            ->bulkActions([
                BulkAction::make('bulk_mark_paid')
                    ->label('Mark All Paid')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $records->each(function (MealRequest $record) {
                            if (! $record->is_nss && ! $record->is_paid) {
                                $record->markAsPaid();
                            }
                        });
                        Notification::make()->title('Marked as paid')->success()->send();
                    }),

                BulkAction::make('bulk_mark_served')
                    ->label('Mark All Served')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $records->each(function (MealRequest $record) {
                            if (! $record->is_served) {
                                $record->markAsServed();
                            }
                        });
                        Notification::make()->title('Marked as served')->success()->send();
                    }),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        $query = MealRequest::query()
            ->with(['user.department', 'weeklyMenuItem.mealItem', 'weeklyMenuItem.weeklyMenu']);

        // Filter by date
        if ($this->selectedDate) {
            $dayOfWeek = strtolower(date('l', strtotime($this->selectedDate)));
            $query->whereHas('weeklyMenuItem', function (Builder $q) use ($dayOfWeek) {
                $q->where('day_of_week', $dayOfWeek);
            });
        }

        // Filter by department
        if ($this->selectedDepartment) {
            $query->whereHas('user', function (Builder $q) {
                $q->where('department_id', $this->selectedDepartment);
            });
        }

        return $query;
    }

    public function getDepartmentOptions(): array
    {
        return Department::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getStats(): array
    {
        $query = $this->getTableQuery();

        return [
            'total' => $query->count(),
            'staff' => (clone $query)->where('is_nss', false)->count(),
            'nss' => (clone $query)->where('is_nss', true)->count(),
            'paid' => (clone $query)->where('is_paid', true)->where('is_nss', false)->count(),
            'pending' => (clone $query)->where('is_paid', false)->where('is_nss', false)->count(),
            'served' => (clone $query)->where('is_served', true)->count(),
            'total_paid' => (clone $query)->where('is_paid', true)->sum('amount_due'),
            'total_pending' => (clone $query)->where('is_paid', false)->where('is_nss', false)->sum('amount_due'),
        ];
    }

    public function updatedSelectedDate(): void
    {
        $this->resetTable();
    }

    public function updatedSelectedDepartment(): void
    {
        $this->resetTable();
    }
}
