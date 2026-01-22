<?php

namespace App\Filament\Widgets\HR;

use App\Models\Memo;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class RecentMemosWidget extends Widget
{
    protected static string $view = 'filament.widgets.hr.recent-memos-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 20;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user?->hasRole('hr_manager');
    }

    public function getViewData(): array
    {
        // Get recent memos
        $recentMemos = Memo::with('recipients')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($memo) {
                $totalRecipients = $memo->recipients->count();
                $readCount = $memo->recipients->whereNotNull('read_at')->count();

                return [
                    'id' => $memo->id,
                    'subject' => $memo->subject ?? $memo->title ?? 'Memo #'.$memo->id,
                    'createdAt' => $memo->created_at?->diffForHumans(),
                    'totalRecipients' => $totalRecipients,
                    'readCount' => $readCount,
                    'readPercentage' => $totalRecipients > 0 ? round(($readCount / $totalRecipients) * 100) : 0,
                ];
            });

        return [
            'recentMemos' => $recentMemos,
            'createUrl' => '/admin/memos/create',
            'viewAllUrl' => '/admin/memos',
        ];
    }
}
