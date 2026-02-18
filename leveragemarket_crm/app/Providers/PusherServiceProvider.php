<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;

class PusherServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('pusher.config', function () {
            $settings = Setting::all()->pluck('value', 'name')->toArray();

            return [
                'driver'=>'pusher',
                'key' => $settings['pusher_key'] ?? env('PUSHER_APP_KEY'),
                'secret' => $settings['pusher_secret'] ?? env('PUSHER_APP_SECRET'),
                'app_id' => $settings['pusher_app_id'] ?? env('PUSHER_APP_ID'),
                'options' => [
                    'cluster' => $settings['pusher_cluster'] ?? env('PUSHER_APP_CLUSTER'),
                    'useTLS' => true,
                ],
            ];
        });
    }

    public function boot()
    {
        Config::set('broadcasting.connections.pusher', $this->app->make('pusher.config'));
    }
}
