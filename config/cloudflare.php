<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudflare API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Cloudflare DNS Configurator
    | plugin. You need to set your Cloudflare API token and zone ID here.
    |
    */

    // Your Cloudflare API token
    // You can generate this at: https://dash.cloudflare.com/profile/api-tokens
    'api_token' => env('CLOUDFLARE_API_TOKEN', ''),

    // Your Cloudflare zone ID
    // You can find this in your domain's DNS settings page
    'zone_id' => env('CLOUDFLARE_ZONE_ID', ''),

    // Default TTL for DNS records (in seconds)
    // Set to 1 for auto TTL (recommended for proxied records)
    'default_ttl' => env('CLOUDFLARE_DEFAULT_TTL', 1),

    // Whether to proxy DNS records by default
    // This enables Cloudflare's proxy features (DDoS protection, etc.)
    'default_proxied' => env('CLOUDFLARE_DEFAULT_PROXIED', true),

    // Domain suffix to append to server names
    // Example: if set to '.example.com', a server named 'minecraft' will create 'minecraft.example.com'
    'domain_suffix' => env('CLOUDFLARE_DOMAIN_SUFFIX', ''),

    // Whether to enable debug logging
    'debug' => env('CLOUDFLARE_DEBUG', false),

    // Server type filtering
    // Only process servers that match these criteria
    'server_types' => [
        // Minecraft-related egg names (case-insensitive partial matches)
        'egg_names' => env('CLOUDFLARE_EGG_NAMES', 'minecraft,minecraft java,minecraft bedrock,minecraft forge,minecraft fabric,minecraft paper,minecraft spigot,minecraft bukkit,minecraft vanilla,minecraft modded'),
        
        // Minecraft-related nest names (case-insensitive partial matches)
        'nest_names' => env('CLOUDFLARE_NEST_NAMES', 'minecraft,game servers,gaming,games'),
        
        // Whether to enable server type filtering
        'enabled' => env('CLOUDFLARE_SERVER_TYPE_FILTERING', true),
    ],
];
