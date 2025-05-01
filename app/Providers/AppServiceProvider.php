<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use App\Models\Notification;

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
        config(['session.lifetime' => 120]);
        config(['session.expire_on_close' => false]);
        config(['session.same_site' => 'lax']);

        View::composer('layouts.app', function ($view) {
            $unreadCount = 0;
            if (auth()->check()) {
                $unreadCount = Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->count();
            }
            $view->with('unreadCount', $unreadCount);
        });
    }
}
