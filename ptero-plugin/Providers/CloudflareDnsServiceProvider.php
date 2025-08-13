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

        // Load the plugin
        $this->loadPlugin();
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
