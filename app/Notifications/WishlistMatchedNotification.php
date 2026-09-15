<?php

namespace App\Notifications;

use App\Models\Wishlist;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class WishlistMatchedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Wishlist $wishlist,
        public string $matchedPotentialName,
        public ?string $requesterVillageName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Permintaan Wishlist Masuk')
            ->body(sprintf(
                'Desa %s membutuhkan "%s" yang cocok dengan potensi desa Anda: %s.',
                $this->requesterVillageName ?? 'tidak dikenal',
                $this->wishlist->title,
                $this->matchedPotentialName,
            ))
            ->icon('heroicon-o-gift')
            ->iconColor('success')
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('Lihat Wishlist')
                    ->url(url('/admin/wishlists/' . $this->wishlist->id . '/edit'))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }

    public function toArray(object $notifiable): array
    {
        return [
            'wishlist_id' => $this->wishlist->id,
            'village_id' => $this->wishlist->village_id,
            'title' => $this->wishlist->title,
            'category' => $this->wishlist->category,
            'matched_potential' => $this->matchedPotentialName,
        ];
    }
}
