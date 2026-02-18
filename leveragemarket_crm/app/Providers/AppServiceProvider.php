<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind('App\Services\PusherService', function ($app) {
            return new \App\Services\PusherService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('layouts.crm.partials.header', function ($view) {
			$promotions = \App\Models\Promotation::where('status', 1)->get();
			$view->with('promotions', $promotions);
		});
		Paginator::useBootstrap();
    }
}
