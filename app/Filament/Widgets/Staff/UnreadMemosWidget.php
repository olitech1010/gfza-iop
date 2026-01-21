<?php

namespace App\Filament\Widgets\Staff;

use App\Models\Memo;
use App\Models\MemoRecipient;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class UnreadMemosWidget extends Widget
{
    protected static string $view = 'filament.widgets.staff.unread-memos-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 20;

    public function getViewData(): array
    {
        $user = Auth::user();

        // Get unread memos for the user
        $unreadCount = MemoRecipient::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        // Get latest 3 unread memos
        $latestMemos = MemoRecipient::where('user_id', $user->id)
            ->whereNull('read_at')
            ->with('memo')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($recipient) {
                return [
                    'id' => $recipient->memo->id,
                    'subject' => $recipient->memo->subject ?? $recipient->memo->title ?? 'Memo',
                    'created_at' => $recipient->memo->created_at?->diffForHumans(),
                ];
            });

        return [
            'unreadCount' => $unreadCount,
            'latestMemos' => $latestMemos,
            'viewAllUrl' => '/admin/memos',
        ];
    }
}
