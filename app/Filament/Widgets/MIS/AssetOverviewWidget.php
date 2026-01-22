<?php

namespace App\Filament\Widgets\MIS;

use App\Models\MisAsset;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AssetOverviewWidget extends Widget
{
    protected static string $view = 'filament.widgets.mis.asset-overview-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 10;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user?->hasRole(['mis_support', 'super_admin']);
    }

    public function getViewData(): array
    {
        $totalAssets = MisAsset::count();
        $assignedAssets = MisAsset::whereNotNull('assigned_to_user_id')->count();
        $availableAssets = MisAsset::whereNull('assigned_to_user_id')->count();
        $underMaintenance = MisAsset::where('status', 'maintenance')->count();

        return [
            'totalAssets' => $totalAssets,
            'assignedAssets' => $assignedAssets,
            'availableAssets' => $availableAssets,
            'underMaintenance' => $underMaintenance,
            'viewAllUrl' => '/admin/mis-assets',
        ];
    }
}
