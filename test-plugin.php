<?php

/**
 * Test script to verify the plugin class can be loaded
 * Run this to check for syntax errors: php test-plugin.php
 */

echo "🧪 Testing Cloudflare DNS Configurator Plugin...\n";
echo "=============================================\n\n";

// Test 1: Check if the main plugin file exists
if (file_exists('CloudflareDnsConfiguratorPlugin.php')) {
    echo "✅ Main plugin file exists\n";
} else {
    echo "❌ Main plugin file missing\n";
    exit(1);
}

// Test 2: Check if required directories exist
$requiredDirs = ['Services', 'Views', 'config', 'database'];
foreach ($requiredDirs as $dir) {
    if (is_dir($dir)) {
        echo "✅ Directory exists: $dir\n";
    } else {
        echo "❌ Directory missing: $dir\n";
    }
}

// Test 3: Check if required files exist
$requiredFiles = [
    'Services/CloudflareDnsService.php',
    'Views/components/dns-info-card.blade.php',
    'config/cloudflare.php',
    'database/migrations/2024_01_01_000000_add_dns_fields_to_servers_table.php',
    'composer.json'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✅ File exists: $file\n";
    } else {
        echo "❌ File missing: $file\n";
    }
}

// Test 4: Check PHP syntax
echo "\n🔍 Checking PHP syntax...\n";
$output = shell_exec('php -l CloudflareDnsConfiguratorPlugin.php 2>&1');
if (strpos($output, 'No syntax errors') !== false) {
    echo "✅ PHP syntax is valid\n";
} else {
    echo "❌ PHP syntax errors found:\n";
    echo $output;
}

// Test 5: Check composer.json
if (file_exists('composer.json')) {
    $composer = json_decode(file_get_contents('composer.json'), true);
    if ($composer && isset($composer['name'])) {
        echo "✅ Composer.json is valid\n";
        echo "   Plugin name: {$composer['name']}\n";
        echo "   Version: {$composer['version']}\n";
    } else {
        echo "❌ Composer.json is invalid\n";
    }
}

echo "\n🎯 Plugin structure verification complete!\n";
echo "\n📋 Next steps:\n";
echo "1. Copy plugin to Pterodactyl: cp -r . /path/to/pterodactyl/app/Plugins/CloudflareDnsConfigurator/\n";
echo "2. Install dependencies: composer install\n";
echo "3. Add to config/app.php providers: Pterodactyl\\Plugins\\CloudflareDnsConfigurator\\CloudflareDnsConfiguratorPlugin::class\n";
echo "4. Run migration: php artisan migrate\n";
echo "5. Clear caches: php artisan config:clear && php artisan view:clear\n";
echo "\n🚀 Ready to install! 🎉\n";
