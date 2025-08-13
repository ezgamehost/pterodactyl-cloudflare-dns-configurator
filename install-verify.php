<?php

/**
 * Installation Verification Script
 * 
 * Run this script to verify your plugin installation:
 * php install-verify.php
 */

echo "🔍 Cloudflare DNS Configurator - Installation Verification\n";
echo "========================================================\n\n";

// Check if we're in the right directory
if (!file_exists('CloudflareDnsConfiguratorPlugin.php')) {
    echo "❌ Error: Please run this script from the plugin root directory\n";
    exit(1);
}

echo "✅ Plugin files found\n";

// Check required files
$requiredFiles = [
    'CloudflareDnsConfiguratorPlugin.php',
    'Services/CloudflareDnsService.php',
    'Providers/CloudflareDnsServiceProvider.php',
    'config/cloudflare.php',
    'Views/components/dns-info-card.blade.php',
    'composer.json'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file\n";
    } else {
        echo "❌ Missing: $file\n";
    }
}

echo "\n📋 Installation Steps:\n";
echo "1. Copy this plugin to: /path/to/pterodactyl/app/Plugins/CloudflareDnsConfigurator/\n";
echo "2. Run: composer install\n";
echo "3. Add to config/app.php providers:\n";
echo "   Pterodactyl\\Plugins\\CloudflareDnsConfigurator\\Providers\\CloudflareDnsServiceProvider::class\n";
echo "4. Publish configuration: php artisan vendor:publish --tag=cloudflare-dns-config\n";
echo "5. Publish views: php artisan vendor:publish --tag=cloudflare-dns-configurator-views\n";
echo "6. Run migration: php artisan migrate\n";
echo "7. Clear caches: php artisan config:clear && php artisan view:clear\n\n";

echo "🔧 Publish Tags Available:\n";
echo "- cloudflare-dns-config (configuration)\n";
echo "- cloudflare-dns-configurator-views (view components)\n\n";

echo "📚 Documentation:\n";
echo "- README.md - Full installation guide\n";
echo "- QUICKSTART.md - Quick setup guide\n";
echo "- INSTALLATION.md - Component installation\n";
echo "- COMPONENTS.md - Available components\n\n";

echo "🎯 Next Steps:\n";
echo "1. Follow the installation steps above\n";
echo "2. Configure your Cloudflare API credentials\n";
echo "3. Test with a Minecraft server\n";
echo "4. Check logs for plugin activity\n\n";

echo "✅ Verification complete!\n";
