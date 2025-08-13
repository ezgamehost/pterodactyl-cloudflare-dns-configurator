<?php

namespace Pterodactyl\Plugins\CloudflareDnsConfigurator;

use Pterodactyl\Contracts\Plugin\PluginInterface;
use Pterodactyl\Services\Servers\ServerCreationService;
use Pterodactyl\Services\Servers\ServerDeletionService;
use Pterodactyl\Events\Server\ServerCreated;
use Pterodactyl\Events\Server\ServerDeleted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class CloudflareDnsConfiguratorPlugin implements PluginInterface
{
    /**
     * The plugin's name.
     */
    public const NAME = 'Cloudflare DNS Configurator';

    /**
     * The plugin's version.
     */
    public const VERSION = '1.0.0';

    /**
     * The plugin's description.
     */
    public const DESCRIPTION = 'Automatically configures Cloudflare DNS records when Pterodactyl servers are created or deleted.';

    /**
     * Boot the plugin.
     */
    public function boot(): void
    {
        // Register event listeners
        Event::listen(ServerCreated::class, [$this, 'onServerCreated']);
        Event::listen(ServerDeleted::class, [$this, 'onServerDeleted']);

        // Add custom fields to server model
        $this->addCustomFields();

        Log::info('Cloudflare DNS Configurator plugin booted successfully');
        
        // Debug: Check configuration status
        if (config('cloudflare.debug', false)) {
            $this->logConfigurationStatus();
        }
    }

    /**
     * Add custom fields to the server model for DNS information.
     */
    private function addCustomFields(): void
    {
        // Extend the server model to add DNS-related fields
        \Pterodactyl\Models\Server::extend(function ($model) {
            $model->casts['dns_name'] = 'string';
            $model->casts['dns_configured'] = 'boolean';
            $model->casts['dns_last_updated'] = 'datetime';
        });

        // Add accessor for the full DNS name
        \Pterodactyl\Models\Server::extend(function ($model) {
            $model->addAccessor('full_dns_name', function () {
                if ($this->dns_name) {
                    $domainSuffix = config('cloudflare.domain_suffix', '');
                    return $this->dns_name . $domainSuffix;
                }
                return null;
            });
        });
    }

    /**
     * Log the current configuration status for debugging.
     */
    private function logConfigurationStatus(): void
    {
        $dnsService = app(\Pterodactyl\Plugins\CloudflareDnsConfigurator\Services\CloudflareDnsService::class);
        
        Log::info('Cloudflare DNS Configurator - Configuration Status', [
            'api_token_configured' => !empty(config('cloudflare.api_token')),
            'zone_id_configured' => !empty(config('cloudflare.zone_id')),
            'domain_suffix' => config('cloudflare.domain_suffix', 'Not set'),
            'default_proxied' => config('cloudflare.default_proxied', true),
            'default_ttl' => config('cloudflare.default_ttl', 1),
            'debug_enabled' => config('cloudflare.debug', false),
            'service_configured' => $dnsService->isConfigured(),
        ]);
    }

    /**
     * Handle server creation event.
     */
    public function onServerCreated(ServerCreated $event): void
    {
        $server = $event->server;
        
        Log::info("Cloudflare DNS Configurator - Server Created Event Triggered", [
            'server_name' => $server->name,
            'server_uuid' => $server->uuid,
            'server_id' => $server->id,
            'timestamp' => now()->toISOString(),
        ]);

        // Only process Minecraft servers
        if (!$this->isMinecraftServer($server)) {
            Log::info("Cloudflare DNS Configurator - Skipping non-Minecraft server", [
                'server_name' => $server->name,
                'egg_name' => $server->egg->name ?? 'Unknown',
                'nest_name' => $server->nest->name ?? 'Unknown',
            ]);
            return;
        }

        try {
            $this->createDnsRecord($server);
            Log::info("DNS record created for Minecraft server: {$server->name} ({$server->uuid})");
        } catch (\Exception $e) {
            Log::error("Failed to create DNS record for Minecraft server: {$server->name}", [
                'error' => $e->getMessage(),
                'server_id' => $event->server->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle server deletion event.
     */
    public function onServerDeleted(ServerDeleted $event): void
    {
        $server = $event->server;
        
        Log::info("Cloudflare DNS Configurator - Server Deleted Event Triggered", [
            'server_name' => $server->name,
            'server_uuid' => $server->uuid,
            'server_id' => $server->id,
            'timestamp' => now()->toISOString(),
        ]);

        // Only process Minecraft servers
        if (!$this->isMinecraftServer($server)) {
            Log::info("Cloudflare DNS Configurator - Skipping non-Minecraft server deletion", [
                'server_name' => $server->name,
                'egg_name' => $server->egg->name ?? 'Unknown',
                'nest_name' => $server->nest->name ?? 'Unknown',
            ]);
            return;
        }

        try {
            $this->deleteDnsRecord($server);
            Log::info("DNS record deleted for Minecraft server: {$server->name} ({$server->uuid})");
        } catch (\Exception $e) {
            Log::error("Failed to delete DNS record for Minecraft server: {$server->name}", [
                'error' => $e->getMessage(),
                'server_id' => $event->server->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Create a DNS record in Cloudflare.
     */
    private function createDnsRecord($server): void
    {
        Log::info("Cloudflare DNS Configurator - Starting DNS record creation", [
            'server_name' => $server->name,
            'server_uuid' => $server->uuid,
        ]);

        $dnsService = app(\Pterodactyl\Plugins\CloudflareDnsConfigurator\Services\CloudflareDnsService::class);
        
        if (!$dnsService->isConfigured()) {
            Log::warning('Cloudflare DNS service not configured, skipping DNS record creation', [
                'server_name' => $server->name,
                'service_configured' => $dnsService->isConfigured(),
            ]);
            return;
        }

        $serverName = $server->name;
        $domainSuffix = config('cloudflare.domain_suffix', '');
        
        if ($domainSuffix) {
            $serverName = $serverName . $domainSuffix;
        }

        Log::info("Cloudflare DNS Configurator - DNS record details", [
            'original_server_name' => $server->name,
            'final_dns_name' => $serverName,
            'domain_suffix' => $domainSuffix,
        ]);

        // Get the server's external IP address
        $ipAddress = $this->getServerIpAddress($server);
        
        if (!$ipAddress) {
            Log::error("Could not determine IP address for server: {$server->name}", [
                'server_id' => $server->id,
                'server_uuid' => $server->uuid,
            ]);
            return;
        }

        Log::info("Cloudflare DNS Configurator - IP address resolved", [
            'server_name' => $server->name,
            'ip_address' => $ipAddress,
        ]);

        $proxied = config('cloudflare.default_proxied', true);
        // Create Minecraft-compliant SRV record instead of just A record
        $success = $dnsService->createMinecraftSrvRecord($serverName, $ipAddress, 25565, $proxied);

        if ($success) {
            // Store DNS information in the server model
            $this->updateServerDnsInfo($server, $serverName, true);
            
            Log::info("Cloudflare DNS Configurator - DNS record created successfully", [
                'server_name' => $server->name,
                'dns_name' => $serverName,
                'ip_address' => $ipAddress,
                'proxied' => $proxied,
            ]);
        } else {
            Log::error("Failed to create DNS record for server: {$server->name}", [
                'dns_name' => $serverName,
                'ip_address' => $ipAddress,
                'proxied' => $proxied,
            ]);
        }
    }

    /**
     * Delete a DNS record from Cloudflare.
     */
    private function deleteDnsRecord($server): void
    {
        Log::info("Cloudflare DNS Configurator - Starting DNS record deletion", [
            'server_name' => $server->name,
            'server_uuid' => $server->uuid,
        ]);

        $dnsService = app(\Pterodactyl\Plugins\CloudflareDnsConfigurator\Services\CloudflareDnsService::class);
        
        if (!$dnsService->isConfigured()) {
            Log::warning('Cloudflare DNS service not configured, skipping DNS record deletion', [
                'server_name' => $server->name,
                'service_configured' => $dnsService->isConfigured(),
            ]);
            return;
        }

        $serverName = $server->name;
        $domainSuffix = config('cloudflare.domain_suffix', '');
        
        if ($domainSuffix) {
            $serverName = $serverName . $domainSuffix;
        }

        Log::info("Cloudflare DNS Configurator - DNS record deletion details", [
            'original_server_name' => $server->name,
            'final_dns_name' => $serverName,
            'domain_suffix' => $domainSuffix,
        ]);

        // Get the server's external IP address
        $ipAddress = $this->getServerIpAddress($server);
        
        if (!$ipAddress) {
            Log::error("Could not determine IP address for server: {$server->name}", [
                'server_id' => $server->id,
                'server_uuid' => $server->uuid,
            ]);
            return;
        }

        Log::info("Cloudflare DNS Configurator - IP address resolved for deletion", [
            'server_name' => $server->name,
            'ip_address' => $ipAddress,
        ]);

        // Delete Minecraft SRV record and associated A record
        $success = $dnsService->deleteMinecraftSrvRecord($serverName, $ipAddress, 25565);

        if ($success) {
            // Clear DNS information from the server model
            $this->updateServerDnsInfo($server, null, false);
            
            Log::info("Cloudflare DNS Configurator - DNS record deleted successfully", [
                'server_name' => $server->name,
                'dns_name' => $serverName,
                'ip_address' => $ipAddress,
            ]);
        } else {
            Log::error("Failed to delete DNS record for server: {$server->name}", [
                'dns_name' => $serverName,
                'ip_address' => $ipAddress,
            ]);
        }
    }

    /**
     * Update the server model with DNS information.
     */
    private function updateServerDnsInfo($server, string $dnsName, bool $configured = true): void
    {
        try {
            $server->update([
                'dns_name' => $dnsName,
                'dns_configured' => $configured,
                'dns_last_updated' => now(),
            ]);

            Log::info("Cloudflare DNS Configurator - Server DNS info updated", [
                'server_name' => $server->name,
                'dns_name' => $dnsName,
                'configured' => $configured,
            ]);
        } catch (\Exception $e) {
            Log::error("Cloudflare DNS Configurator - Failed to update server DNS info", [
                'server_name' => $server->name,
                'dns_name' => $dnsName,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if the server is a Minecraft server.
     */
    private function isMinecraftServer($server): bool
    {
        try {
            // Check if server type filtering is enabled
            if (!config('cloudflare.server_types.enabled', true)) {
                Log::info("Cloudflare DNS Configurator - Server type filtering disabled, processing all servers", [
                    'server_name' => $server->name,
                ]);
                return true;
            }

            // Check if server has egg and nest relationships
            if (!$server->egg || !$server->nest) {
                Log::warning("Cloudflare DNS Configurator - Server missing egg or nest information", [
                    'server_name' => $server->name,
                    'has_egg' => isset($server->egg),
                    'has_nest' => isset($server->nest),
                ]);
                return false;
            }

            $eggName = strtolower($server->egg->name ?? '');
            $nestName = strtolower($server->nest->name ?? '');

            // Get configured egg and nest names from config
            $configuredEggNames = array_map('trim', explode(',', config('cloudflare.server_types.egg_names', '')));
            $configuredNestNames = array_map('trim', explode(',', config('cloudflare.server_types.nest_names', '')));

            // Check if egg name contains configured terms
            $isMinecraftEgg = collect($configuredEggNames)->contains(function ($name) use ($eggName) {
                return !empty($name) && str_contains($eggName, strtolower($name));
            });

            // Check if nest name contains configured terms
            $isMinecraftNest = collect($configuredNestNames)->contains(function ($name) use ($nestName) {
                return !empty($name) && str_contains($nestName, strtolower($name));
            });

            $isMinecraft = $isMinecraftEgg || $isMinecraftNest;

            Log::info("Cloudflare DNS Configurator - Server type check", [
                'server_name' => $server->name,
                'egg_name' => $server->egg->name,
                'nest_name' => $server->nest->name,
                'configured_egg_names' => $configuredEggNames,
                'configured_nest_names' => $configuredNestNames,
                'is_minecraft_egg' => $isMinecraftEgg,
                'is_minecraft_nest' => $isMinecraftNest,
                'is_minecraft_server' => $isMinecraft,
            ]);

            return $isMinecraft;

        } catch (\Exception $e) {
            Log::error("Cloudflare DNS Configurator - Error checking server type", [
                'server_name' => $server->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Get the server's external IP address.
     */
    private function getServerIpAddress($server): ?string
    {
        // Try to get the external IP from the server's allocation
        if ($server->allocation && $server->allocation->ip) {
            return $server->allocation->ip;
        }

        // Fallback to getting from allocations
        $allocation = $server->allocations()->first();
        if ($allocation && $allocation->ip) {
            return $allocation->ip;
        }

        return null;
    }

    /**
     * Get the plugin's name.
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * Get the plugin's version.
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Get the plugin's description.
     */
    public function getDescription(): string
    {
        return self::DESCRIPTION;
    }
}
