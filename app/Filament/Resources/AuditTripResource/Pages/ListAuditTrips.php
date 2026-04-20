<?php

namespace App\Filament\Resources\AuditTripResource\Pages;

use App\Filament\Resources\AuditTripResource;
use App\Models\AuditTrip;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Livewire\WithPagination;

class ListAuditTrips extends Page
{
    use WithPagination;

    protected static string $resource = AuditTripResource::class;

    protected static string $view = 'filament.pages.audit-trips-kanban';

    public string $activeTab = 'all';
    public string $teamFilter = '';
    public string $typeFilter = '';
    public string $searchQuery = '';

    protected $queryString = [
        'activeTab' => ['except' => 'all'],
        'teamFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'searchQuery' => ['except' => ''],
    ];

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

    public function getTrips()
    {
        $query = AuditTrip::with(['driver.user', 'vehicle']);

        if ($this->activeTab !== 'all') {
            $query->where('status', $this->activeTab);
        }

        if ($this->teamFilter) {
            $query->where('team_name', $this->teamFilter);
        }

        if ($this->typeFilter) {
            $query->where('audit_type', $this->typeFilter);
        }

        if ($this->searchQuery) {
            $query->where(function ($q) {
                $q->where('company_name', 'like', "%{$this->searchQuery}%")
                  ->orWhere('team_members', 'like', "%{$this->searchQuery}%")
                  ->orWhere('region', 'like', "%{$this->searchQuery}%")
                  ->orWhereHas('driver.user', function ($dq) {
                      $dq->where('name', 'like', "%{$this->searchQuery}%");
                  });
            });
        }

        return $query->orderBy('team_name')->orderBy('sequence_number')->paginate(18);
    }

    public function getTeams()
    {
        return AuditTrip::distinct()->pluck('team_name')->sort()->values();
    }

    public function getCounts()
    {
        return [
            'all' => AuditTrip::count(),
            'scheduled' => AuditTrip::where('status', 'scheduled')->count(),
            'in_progress' => AuditTrip::where('status', 'in_progress')->count(),
            'completed' => AuditTrip::where('status', 'completed')->count(),
        ];
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatedSearchQuery(): void
    {
        $this->resetPage();
    }

    public function updatedTeamFilter(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function markInProgress(int $id): void
    {
        AuditTrip::where('id', $id)->update(['status' => 'in_progress']);
    }

    public function markCompleted(int $id): void
    {
        AuditTrip::where('id', $id)->update(['status' => 'completed']);
    }
}
