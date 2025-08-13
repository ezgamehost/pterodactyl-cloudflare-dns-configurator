<?php

namespace Pterodactyl\Plugins\CloudflareDnsConfigurator\Providers;

use Illuminate\Support\ServiceProvider;
use Pterodactyl\Plugins\CloudflareDnsConfigurator\Services\CloudflareDnsService;
use Pterodactyl\Plugins\CloudflareDnsConfigurator\Providers\ViewServiceProvider;

class CloudflareDnsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the Cloudflare DNS service as a singleton
        $this->app->singleton(CloudflareDnsService::class, function ($app) {
            return new CloudflareDnsService();
        });

        // Merge the configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/cloudflare.php', 'cloudflare'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish the configuration file
        $this->publishes([
            __DIR__ . '/../config/cloudflare.php' => config_path('cloudflare.php'),
        ], 'cloudflare-dns-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/../Views' => resource_path('views/vendor/cloudflare-dns-configurator'),
        ], 'cloudflare-dns-configurator-views');

        // Load views from the plugin
        $this->loadViewsFrom(__DIR__ . '/../Views', 'cloudflare-dns-configurator');

        // Register view components
        $this->registerViewComponents();

        // Load the plugin
        $this->loadPlugin();

        // Log that the service provider has booted
        if (config('cloudflare.debug', false)) {
            \Illuminate\Support\Facades\Log::info('CloudflareDnsServiceProvider booted successfully');
        }
    }

    /**
     * Register view components.
     */
    private function registerViewComponents(): void
    {
        // Register Blade components
        \Illuminate\Support\Facades\Blade::component('dns-info-card', \Pterodactyl\Plugins\CloudflareDnsConfigurator\Views\Components\DnsInfoCard::class);
        \Illuminate\Support\Facades\Blade::component('server-connection-info', \Pterodactyl\Plugins\CloudflareDnsConfigurator\Views\Components\ServerConnectionInfo::class);
        \Illuminate\Support\Facades\Blade::component('server-connection-compact', \Pterodactyl\Plugins\CloudflareDnsConfigurator\Views\Components\ServerConnectionCompact::class);
    }

    /**
     * Load the plugin.
     */
    private function loadPlugin(): void
    {
        $plugin = new \Pterodactyl\Plugins\CloudflareDnsConfigurator\CloudflareDnsConfiguratorPlugin();
        $plugin->boot();
    }
}
