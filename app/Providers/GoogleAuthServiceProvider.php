<?php

namespace App\Providers;

use App\Resource\ClientContract;
use App\Resource\GoogleApiClient;
use Google_Client;
use Illuminate\Support\ServiceProvider;

class GoogleAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ClientContract::class, function () {
            $api = new GoogleApiClient(new Google_Client());
            $api->setAccessType(config('google.access_type'));
            $api->setApplicationName(config('google.application_name'));
            $api->setConfig(config('google.credentials_path'));
            return $api;
        });
    }

}
