<?php

namespace App\Console\Commands;

use App\Models\AssetWarranty;
use App\Models\User;
use App\Notifications\AssetOperationNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendWarrantyExpiryReminders extends Command
{
    protected $signature = 'assets:send-warranty-reminders';

    protected $description = 'Notify administrators and managers about upcoming warranty expirations';

    public function handle(): int
    {
        $reminderDays = [30, 7, 1];
        $startOfDay = today();
        $endOfDay = today()->addDays(max($reminderDays));
        $recipients = User::query()
            ->where('is_active', true)
            ->whereHas('role', fn ($query) => $query->whereIn('slug', [User::ROLE_ADMIN, User::ROLE_MANAGER]))
            ->get();

        AssetWarranty::query()
            ->with('asset')
            ->whereBetween('expires_at', [$startOfDay->toDateString(), $endOfDay->toDateString()])
            ->chunkById(100, function ($warranties) use ($recipients, $reminderDays): void {
                foreach ($warranties as $warranty) {
                    $daysLeft = (int) today()->diffInDays($warranty->expires_at, false);
                    if (! in_array($daysLeft, $reminderDays, true)) {
                        continue;
                    }

                    $sentDays = $warranty->reminder_days_sent ?? [];
                    if (in_array($daysLeft, $sentDays, true)) {
                        continue;
                    }

                    Notification::send($recipients, new AssetOperationNotification(
                        kind: 'warranty_expiry',
                        title: 'Warranty expiring soon',
                        message: "{$warranty->asset?->asset_code} warranty expires in {$daysLeft} day(s).",
                        url: route('operations.warranties'),
                        details: [
                            'asset_id' => $warranty->asset_id,
                            'warranty_id' => $warranty->id,
                            'expires_at' => $warranty->expires_at->toDateString(),
                            'days_left' => $daysLeft,
                        ],
                    ));

                    $warranty->update(['reminder_days_sent' => [...$sentDays, $daysLeft]]);
                }
            });

        $this->info('Warranty expiry reminders processed.');

        return self::SUCCESS;
    }
}
