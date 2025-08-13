#!/bin/bash

# Cloudflare DNS Configurator Plugin - Installation Script
# This script will install the plugin in your Pterodactyl panel

echo "🔧 Cloudflare DNS Configurator Plugin - Installation Script"
echo "=========================================================="
echo ""

# Check if we're in the plugin directory
if [ ! -f "CloudflareDnsConfiguratorPlugin.php" ]; then
    echo "❌ Error: Please run this script from the plugin root directory"
    exit 1
fi

# Get Pterodactyl installation path
echo "📁 Enter the full path to your Pterodactyl installation:"
echo "   Example: /var/www/pterodactyl"
read -p "   Path: " PTERO_PATH

if [ ! -d "$PTERO_PATH" ]; then
    echo "❌ Error: Directory $PTERO_PATH does not exist"
    exit 1
fi

if [ ! -f "$PTERO_PATH/artisan" ]; then
    echo "❌ Error: $PTERO_PATH does not appear to be a Pterodactyl installation (no artisan file)"
    exit 1
fi

echo ""
echo "✅ Pterodactyl installation found at: $PTERO_PATH"
echo ""

# Create plugin directory
PLUGIN_DIR="$PTERO_PATH/app/Plugins/CloudflareDnsConfigurator"
echo "📂 Creating plugin directory..."
mkdir -p "$PLUGIN_DIR"

# Copy plugin files
echo "📋 Copying plugin files..."
cp -r . "$PLUGIN_DIR/"

# Navigate to plugin directory
cd "$PLUGIN_DIR"

# Install dependencies
echo "📦 Installing dependencies..."
if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader
else
    echo "⚠️  Warning: Composer not found. Please install dependencies manually:"
    echo "   cd $PLUGIN_DIR && composer install"
fi

# Copy configuration manually
echo "⚙️  Copying configuration..."
cp config/cloudflare.php "$PTERO_PATH/config/cloudflare.php"

# Copy views manually
echo "🎨 Copying view components..."
mkdir -p "$PTERO_PATH/resources/views/vendor/cloudflare-dns-configurator"
cp -r Views/* "$PTERO_PATH/resources/views/vendor/cloudflare-dns-configurator/"

# Create environment file
echo "🔐 Creating environment file..."
if [ ! -f "$PTERO_PATH/.env" ]; then
    echo "⚠️  Warning: No .env file found. Please create one manually."
else
    echo "" >> "$PTERO_PATH/.env"
    echo "# Cloudflare DNS Configurator Plugin Configuration" >> "$PTERO_PATH/.env"
    echo "CLOUDFLARE_API_TOKEN=your_api_token_here" >> "$PTERO_PATH/.env"
    echo "CLOUDFLARE_ZONE_ID=your_zone_id_here" >> "$PTERO_PATH/.env"
    echo "CLOUDFLARE_DOMAIN_SUFFIX=.yourdomain.com" >> "$PTERO_PATH/.env"
    echo "CLOUDFLARE_DEFAULT_PROXIED=true" >> "$PTERO_PATH/.env"
    echo "CLOUDFLARE_DEBUG=false" >> "$PTERO_PATH/.env"
    echo ""
    echo "✅ Environment variables added to .env file"
    echo "⚠️  Please edit the .env file with your actual Cloudflare credentials"
fi

# Navigate back to Pterodactyl
cd "$PTERO_PATH"

# Run migration
echo "🗄️  Running database migration..."
php artisan migrate

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan view:clear
php artisan cache:clear

echo ""
echo "✅ Installation completed!"
echo ""
echo "📋 Next steps:"
echo "1. Edit $PTERO_PATH/.env with your Cloudflare API credentials"
echo "2. Restart your Pterodactyl panel"
echo "3. Create a Minecraft server to test the plugin"
echo "4. Check logs: tail -f storage/logs/laravel.log"
echo ""
echo "🔧 Service provider registration:"
echo "   Add this line to config/app.php in the providers array:"
echo "   Pterodactyl\\Plugins\\CloudflareDnsConfigurator\\CloudflareDnsConfiguratorPlugin::class"
echo ""
echo "🎯 Test the plugin by creating a Minecraft server in Pterodactyl!"
