<?php

namespace App\Observers;

use App\Models\User;
use App\Models\VillagePotential;
use App\Models\Wishlist;
use App\Notifications\WishlistMatchedNotification;
use Illuminate\Support\Facades\Notification;

class WishlistObserver
{
    public function created(Wishlist $wishlist): void
    {
        $this->notifyMatchingVillages($wishlist);
    }

    /**
     * Find village potentials matching this wishlist and notify each village's
     * admin users. Matching logic: same category, plus fuzzy keyword match
     * on name/description (falls back to category-only if nothing specific).
     */
    protected function notifyMatchingVillages(Wishlist $wishlist): void
    {
        $keywords = $this->extractKeywords($wishlist->title);

        $query = VillagePotential::query()
            ->where('is_active', true)
            ->where('village_id', '!=', $wishlist->village_id)
            ->where('category', $wishlist->category);

        if (!empty($keywords)) {
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->orWhere('name', 'like', "%{$kw}%")
                      ->orWhere('description', 'like', "%{$kw}%");
                }
            });
        }

        $matchedVillageIds = $query->pluck('village_id')->unique()->values();

        if ($matchedVillageIds->isEmpty()) {
            return;
        }

        $adminUsers = User::query()
            ->whereIn('village_id', $matchedVillageIds)
            ->whereIn('user_level', ['village_admin', 'superadmin'])
            ->where('is_active', true)
            ->get();

        if ($adminUsers->isEmpty()) {
            return;
        }

        $requesterVillageName = $wishlist->village?->name;

        foreach ($adminUsers as $user) {
            $matchedPotential = VillagePotential::query()
                ->where('village_id', $user->village_id)
                ->where('category', $wishlist->category)
                ->value('name') ?? $wishlist->title;

            Notification::send($user, new WishlistMatchedNotification(
                $wishlist,
                $matchedPotential,
                $requesterVillageName,
            ));
        }

        $wishlist->forceFill([
            'notified_count' => $adminUsers->count(),
            'notified_at' => now(),
        ])->saveQuietly();
    }

    protected function extractKeywords(string $text): array
    {
        $stopwords = ['dan', 'atau', 'untuk', 'dari', 'dengan', 'di', 'ke', 'yang', 'saya', 'kami', 'butuh', 'perlu'];
        $words = preg_split('/\s+/', mb_strtolower(trim($text))) ?: [];
        $words = array_filter($words, fn ($w) => mb_strlen($w) >= 3 && !in_array($w, $stopwords, true));
        return array_values(array_unique($words));
    }
}
