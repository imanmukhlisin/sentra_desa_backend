<?php

namespace App\Providers;

use App\Models\Wishlist;
use App\Observers\WishlistObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login; // Import Event Login
use Illuminate\Support\Facades\Event; // Import Facade Event
use Illuminate\Support\Facades\DB; // Import DB untuk manipulasi session jika diperlukan

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Wishlist::observe(WishlistObserver::class);

        /**
         * Logika Single Session:
         * Ketika user login melalui Web Dashboard (Filament), 
         * maka semua token API (Sanctum) akan dihapus (Expired).
         */
        Event::listen(Login::class, function (Login $event) {
            // Kita cek apakah login ini menggunakan guard 'web' (Dashboard Filament)
            if ($event->guard === 'web') {
                $user = $event->user;

                // 1. Hapus semua token Sanctum agar login di aplikasi mobile otomatis logout
                if (method_exists($user, 'tokens')) {
                    $user->tokens()->delete();
                }

                /**
                 * 2. Opsi tambahan: Jika kamu ingin memastikan tidak ada sesi web lain yang nyangkut
                 * (Misal login di Chrome, lalu login lagi di Safari),
                 * hapus baris di tabel sessions kecuali yang sekarang (opsional).
                 */
                DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->where('id', '!=', session()->getId())
                    ->delete();
            }
        });
    }
}