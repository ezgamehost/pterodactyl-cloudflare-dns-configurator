# Quick Start Guide

Get the Cloudflare DNS Configurator plugin running in under 5 minutes!

## 🚀 Quick Installation

### 1. Clone the Plugin
```bash
cd /path/to/your/pterodactyl
git clone https://github.com/yourusername/pterodactyl-cloudflare-dns-configurator.git app/Plugins/CloudflareDnsConfigurator
cd app/Plugins/CloudflareDnsConfigurator
composer install

# Verify installation
php install-verify.php
```

### 2. Quick Configuration
```bash
# Copy and edit environment file
cp env.example .env
nano .env  # Add your Cloudflare API token and zone ID
```

### 3. Install and Activate
```bash
# Publish configuration and views
php artisan vendor:publish --tag=cloudflare-dns-config
php artisan vendor:publish --tag=cloudflare-dns-configurator-views

# Add to config/app.php providers array:
# Pterodactyl\Plugins\CloudflareDnsConfigurator\Providers\CloudflareDnsServiceProvider::class

# Run migration and clear cache
php artisan migrate
php artisan config:clear
php artisan view:clear
```

### 4. Test It
Create a Minecraft server in Pterodactyl and check the logs:
```bash
tail -f storage/logs/laravel.log
# Should show: "Cloudflare DNS Configurator - Server Created Event Triggered"
```

## ⚡ What You Need

- **Cloudflare API Token**: [Create here](https://dash.cloudflare.com/profile/api-tokens)
- **Zone ID**: Found in your domain's DNS settings
- **Domain Suffix**: e.g., `.example.com`

## 🔧 Environment Variables

```env
CLOUDFLARE_API_TOKEN=your_token_here
CLOUDFLARE_ZONE_ID=your_zone_id_here
CLOUDFLARE_DOMAIN_SUFFIX=.yourdomain.com
```

## 📱 Add to Panel

Add this to your server details view:
```blade
@component('cloudflare-dns-configurator::components/server-connection-info', ['server' => $server])
@endcomponent
```

## 🎯 What Happens Next

1. **Create Minecraft server** → Plugin creates DNS record automatically
2. **DNS name appears** in panel instead of IP address
3. **Users can copy** the domain name easily
4. **Delete server** → DNS record removed automatically

## 🐛 Troubleshooting

### Plugin not working?
```bash
# Check if it's loaded
php artisan tinker
>>> app('Pterodactyl\Plugins\CloudflareDnsConfigurator\CloudflareDnsConfiguratorPlugin')

# Check logs
tail -f storage/logs/laravel.log | grep "Cloudflare DNS Configurator"
```

### DNS not showing?
- Verify Cloudflare API token has correct permissions
- Check if server is Minecraft type (egg/nest names)
- Ensure domain suffix is configured

### Components not found?
```bash
php artisan view:clear
php artisan vendor:publish --tag=cloudflare-dns-configurator-views --force
```

## 📚 Next Steps

- Read the [Installation Guide](INSTALLATION.md) for detailed setup
- Check [Available Components](COMPONENTS.md) for customization
- Review the [README](README.md) for full documentation

## 🆘 Need Help?

1. Check the logs: `tail -f storage/logs/laravel.log`
2. Verify configuration: `php artisan config:show cloudflare`
3. Test API connection: Check Cloudflare dashboard for DNS records
4. Open an issue on GitHub with logs and configuration details

---

**That's it!** Your plugin should now automatically manage DNS records for Minecraft servers. 🎉
