# Cloudflare DNS Configurator Plugin for Pterodactyl

A Pterodactyl plugin that automatically configures Cloudflare DNS records when servers are created or deleted.

## Features

- **Automatic DNS Management**: Creates Minecraft-compliant SRV records when servers are created
- **Cleanup**: Removes DNS records when servers are deleted
- **Panel Integration**: Displays DNS information directly in the Pterodactyl panel
- **Minecraft Support**: Creates proper SRV records for Minecraft servers (port 25565)
- **Configurable**: Easy to configure via environment variables
- **Logging**: Comprehensive logging for debugging and monitoring
- **Error Handling**: Graceful error handling with fallbacks

## Requirements

- Pterodactyl Panel 1.0 or higher
- PHP 8.0 or higher
- Laravel 10.0 or higher
- Cloudflare account with API access

## Installation

### 1. Clone the Repository

#### Option A: Clone from Git (Recommended)
```bash
# Navigate to your Pterodactyl installation directory
cd /path/to/your/pterodactyl/installation

# Clone the plugin repository
git clone https://github.com/yourusername/whmcs-pterodactyl-cloudflare-dns-configurator.git app/Plugins/CloudflareDnsConfigurator

# Navigate into the plugin directory
cd app/Plugins/CloudflareDnsConfigurator

# Install dependencies
composer install
```

#### Option B: Download and Extract
1. Download the plugin ZIP file from the releases page
2. Extract it to `app/Plugins/CloudflareDnsConfigurator/` in your Pterodactyl installation
3. Navigate to the plugin directory and run `composer install`

#### Option C: Manual Installation
Place this plugin in your Pterodactyl installation directory under `app/Plugins/CloudflareDnsConfigurator/`.

### 2. Install Dependencies

```bash
# Navigate to the plugin directory (if not already there)
cd app/Plugins/CloudflareDnsConfigurator/

# Install PHP dependencies
composer install

# Verify installation
composer show --installed | grep cloudflare
```

### 3. Publish Configuration and Views

```bash
# Publish configuration
php artisan vendor:publish --tag=cloudflare-dns-config

# Publish view components
php artisan vendor:publish --tag=cloudflare-dns-configurator-views
```

### 4. Configure Environment Variables

Add the following to your `.env` file:

```env
# Cloudflare API Configuration
CLOUDFLARE_API_TOKEN=your_api_token_here
CLOUDFLARE_ZONE_ID=your_zone_id_here
CLOUDFLARE_DOMAIN_SUFFIX=.yourdomain.com
CLOUDFLARE_DEFAULT_PROXIED=true
CLOUDFLARE_DEBUG=false
```

### 5. Register Service Provider

Add the service provider to your `config/app.php` file in the `providers` array:

```php
'providers' => [
    // ... other providers
    Pterodactyl\Plugins\CloudflareDnsConfigurator\CloudflareDnsConfiguratorPlugin::class,
],
```

### 6. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

## Complete Setup Guide

### Prerequisites
- Pterodactyl Panel 1.0 or higher
- PHP 8.0 or higher
- Composer installed
- Git installed (for cloning)
- Cloudflare account with API access

### Step-by-Step Setup

#### 1. Prepare Your Environment
```bash
# Ensure you're in your Pterodactyl installation directory
pwd
# Should show: /path/to/your/pterodactyl/installation

# Check PHP version
php --version
# Should be 8.0 or higher

# Check Composer
composer --version
```

#### 2. Clone and Install Plugin
```bash
# Clone the repository
git clone https://github.com/yourusername/whmcs-pterodactyl-cloudflare-dns-configurator.git app/Plugins/CloudflareDnsConfigurator

# Navigate to plugin directory
cd app/Plugins/CloudflareDnsConfigurator

# Install dependencies
composer install

# Verify the plugin structure
ls -la
# Should show: CloudflareDnsConfiguratorPlugin.php, composer.json, etc.
```

#### 3. Configure Environment
```bash
# Copy the example environment file
cp env.example .env

# Edit the environment file with your Cloudflare credentials
nano .env
# or use your preferred editor: vim .env, code .env, etc.
```

#### 4. Set Up Cloudflare API
1. Go to [Cloudflare Dashboard](https://dash.cloudflare.com/profile/api-tokens)
2. Click "Create Token"
3. Use the "Edit zone DNS" template
4. Set permissions:
   - Zone: DNS:Edit
   - Include: Specific zone → your domain
5. Copy the generated token
6. Add to your `.env` file:
   ```env
   CLOUDFLARE_API_TOKEN=your_actual_token_here
   CLOUDFLARE_ZONE_ID=your_actual_zone_id_here
   CLODFLARE_DOMAIN_SUFFIX=.yourdomain.com
   ```

#### 5. Publish Configuration and Views
```bash
# Publish the plugin configuration
php artisan vendor:publish --tag=cloudflare-dns-config

# Publish the view components
php artisan vendor:publish --tag=cloudflare-dns-configurator-views

# Verify files were published
ls -la config/cloudflare.php
ls -la resources/views/vendor/cloudflare-dns-configurator/
```

#### 6. Register Service Provider
Edit `config/app.php` and add to the `providers` array:
```php
'providers' => [
    // ... other providers
    Pterodactyl\Plugins\CloudflareDnsConfigurator\CloudflareDnsConfiguratorPlugin::class,
],
```

#### 7. Run Database Migration
```bash
# Run the migration to add DNS fields
php artisan migrate

# Verify the migration
php artisan migrate:status
# Should show the DNS fields migration as completed
```

#### 8. Clear Caches
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

#### 9. Test the Installation
```bash
# Check if the plugin is loaded
php artisan tinker
>>> app('Pterodactyl\Plugins\CloudflareDnsConfigurator\CloudflareDnsConfiguratorPlugin')
# Should return the plugin instance

# Check the logs for plugin boot messages
tail -f storage/logs/laravel.log
# Should show: "Cloudflare DNS Configurator plugin booted successfully"
```

#### 10. Add Components to Views
Follow the [Installation Guide](INSTALLATION.md) to add the DNS components to your panel views.

### Verification Checklist
- [ ] Plugin repository cloned to `app/Plugins/CloudflareDnsConfigurator/`
- [ ] Dependencies installed with `composer install`
- [ ] Environment variables configured in `.env`
- [ ] Configuration published with `php artisan vendor:publish --tag=cloudflare-dns-config`
- [ ] Views published with `php artisan vendor:publish --tag=cloudflare-dns-configurator-views`
- [ ] Service provider registered in `config/app.php`
- [ ] Database migration completed
- [ ] Caches cleared (`php artisan config:clear && php artisan view:clear`)
- [ ] Plugin shows in logs as "booted successfully"
- [ ] DNS components added to panel views

## Configuration

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `CLOUDFLARE_API_TOKEN` | Your Cloudflare API token | Required |
| `CLOUDFLARE_ZONE_ID` | Your Cloudflare zone ID | Required |
| `CLOUDFLARE_DOMAIN_SUFFIX` | Domain suffix for server names | None |
| `CLOUDFLARE_DEFAULT_PROXIED` | Whether to proxy DNS records | `true` |
| `CLOUDFLARE_DEBUG` | Enable debug logging | `false` |

### Cloudflare API Token Setup

1. Go to [Cloudflare Dashboard](https://dash.cloudflare.com/profile/api-tokens)
2. Click "Create Token"
3. Use the "Edit zone DNS" template
4. Set permissions:
   - Zone: DNS:Edit
   - Include: Specific zone → your domain
5. Copy the generated token

### Zone ID

1. Go to your domain's DNS settings in Cloudflare
2. The Zone ID is displayed on the right sidebar

## Usage

Once installed and configured, the plugin will automatically:

1. **Create DNS Records**: When a new server is created in Pterodactyl, a Minecraft-compliant SRV record will be automatically created in Cloudflare
2. **Delete DNS Records**: When a server is deleted, the corresponding DNS records will be removed from Cloudflare
3. **Display DNS Info**: DNS information is shown directly in the Pterodactyl panel for easy access

### Example

If you have:
- Domain suffix: `.example.com`
- Server name: `minecraft-server`
- Server IP: `192.168.1.100`

The plugin will create:
- **A Record**: `minecraft-server-direct.example.com` → `192.168.1.100`
- **SRV Record**: `minecraft-server.example.com` → `minecraft-server-direct.example.com:25565`

### DNS Info Card

The plugin adds a DNS information card to your server details page that shows:
- **Server DNS Name**: The full domain name for the server
- **Configuration Status**: Whether DNS is properly configured
- **Last Updated**: When the DNS record was last modified
- **Copy Button**: Easy copying of the DNS name for sharing

To add the DNS info card to your panel, see the [Installation Guide](INSTALLATION.md).

## Logging

The plugin logs all operations to Laravel's log system. Check your logs at `storage/logs/laravel.log` for:

- Successful DNS record creation/deletion
- Configuration warnings
- API errors
- Debug information (when enabled)

## Troubleshooting

### Common Issues

1. **"Cloudflare DNS service not configured"**
   - Check your environment variables
   - Ensure the configuration file is published

2. **"Failed to create DNS record"**
   - Verify your API token has correct permissions
   - Check if the zone ID is correct
   - Ensure the domain exists in Cloudflare

3. **"Could not determine IP address"**
   - Check if the server has proper allocations
   - Verify network configuration

### Debug Mode

Enable debug mode by setting `CLOUDFLARE_DEBUG=true` in your `.env` file for more detailed logging.

## Development

### Project Structure

```
CloudflareDnsConfigurator/
├── CloudflareDnsConfiguratorPlugin.php  # Main plugin class
├── Services/
│   └── CloudflareDnsService.php        # Cloudflare API service
├── Providers/
│   └── CloudflareDnsConfiguratorPlugin.php # Main plugin class (extends ServiceProvider)
├── config/
│   └── cloudflare.php                   # Configuration file
├── composer.json                        # Dependencies
└── README.md                           # This file
```

### Adding New Features

1. Extend the main plugin class
2. Add new methods to the service class
3. Update the configuration if needed
4. Test thoroughly

## License

This plugin is licensed under the MIT License. See the LICENSE file for details.

## Support

For support, please:

1. Check the troubleshooting section above
2. Review the logs for error details
3. Verify your configuration
4. Open an issue on the project repository

## 📚 Additional Guides

- **[Quick Start Guide](QUICKSTART.md)** - Get running in under 5 minutes
- **[Installation Guide](INSTALLATION.md)** - Detailed setup instructions
- **[Components Reference](COMPONENTS.md)** - All available view components
- **[Development Guide](DEVELOPMENT.md)** - For contributors and developers

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
