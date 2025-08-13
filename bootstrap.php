<?php

/**
 * Bootstrap file for Cloudflare DNS Configurator Plugin
 * 
 * Include this file in your Pterodactyl bootstrap process
 * or call it manually to load the plugin.
 */

// Define the plugin namespace
namespace Pterodactyl\Plugins\CloudflareDnsConfigurator;

// Load the plugin directly
$plugin = new CloudflareDnsConfiguratorPlugin();
$plugin->boot();

echo "Cloudflare DNS Configurator Plugin loaded successfully!\n";
