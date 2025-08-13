<?php

namespace Pterodactyl\Plugins\CloudflareDnsConfigurator\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Pterodactyl\Plugins\CloudflareDnsConfigurator\Views\Components\DnsInfoCard;
use Pterodactyl\Plugins\CloudflareDnsConfigurator\Views\Components\ServerConnectionInfo;
use Pterodactyl\Plugins\CloudflareDnsConfigurator\Views\Components\ServerConnectionCompact;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register the view components
        Blade::component('dns-info-card', DnsInfoCard::class);
        Blade::component('server-connection-info', ServerConnectionInfo::class);
        Blade::component('server-connection-compact', ServerConnectionCompact::class);

        // Load views from the plugin
        $this->loadViewsFrom(__DIR__ . '/../Views', 'cloudflare-dns-configurator');

        // Publish views
        $this->publishes([
            __DIR__ . '/../Views' => resource_path('views/vendor/cloudflare-dns-configurator'),
        ], 'cloudflare-dns-configurator-views');
    }
}
