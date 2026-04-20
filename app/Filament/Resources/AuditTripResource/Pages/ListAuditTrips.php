<?php

namespace App\Filament\Resources\AuditTripResource\Pages;

use App\Filament\Resources\AuditTripResource;
use App\Models\AuditTrip;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAuditTrips extends ListRecords
{
    protected static string $resource = AuditTripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\Transport\AuditScheduleStatsWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(AuditTrip::count()),
            'scheduled' => Tab::make('Scheduled')
                ->badge(AuditTrip::where('status', 'scheduled')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'scheduled')),
            'in_progress' => Tab::make('In Progress')
                ->badge(AuditTrip::where('status', 'in_progress')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'in_progress')),
            'completed' => Tab::make('Completed')
                ->badge(AuditTrip::where('status', 'completed')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),
        ];
    }
}
