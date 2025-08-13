# Manual Installation Guide

This guide bypasses the publish tag issues by manually copying files and setting up the plugin.

## 🚀 **Quick Installation (Recommended)**

### **Option 1: Use the Installation Script**
```bash
# Make sure you're in the plugin directory
./install.sh
```

The script will:
- Ask for your Pterodactyl installation path
- Copy all files automatically
- Install dependencies
- Set up configuration
- Run migrations
- Clear caches

### **Option 2: Manual Installation**

#### **Step 1: Copy Plugin Files**
```bash
# Replace /path/to/your/pterodactyl with your actual path
cp -r . /path/to/your/pterodactyl/app/Plugins/CloudflareDnsConfigurator/
```

#### **Step 2: Install Dependencies**
```bash
cd /path/to/your/pterodactyl/app/Plugins/CloudflareDnsConfigurator
composer install
cd /path/to/your/pterodactyl
```

#### **Step 3: Copy Configuration Manually**
```bash
# Copy the configuration file
cp app/Plugins/CloudflareDnsConfigurator/config/cloudflare.php config/cloudflare.php

# Copy view components
mkdir -p resources/views/vendor/cloudflare-dns-configurator
cp -r app/Plugins/CloudflareDnsConfigurator/Views/* resources/views/vendor/cloudflare-dns-configurator/
```

#### **Step 4: Add Environment Variables**
Add these lines to your `.env` file:
```env
# Cloudflare DNS Configurator Plugin
CLOUDFLARE_API_TOKEN=your_api_token_here
CLOUDFLARE_ZONE_ID=your_zone_id_here
CLOUDFLARE_DOMAIN_SUFFIX=.yourdomain.com
CLOUDFLARE_DEFAULT_PROXIED=true
CLOUDFLARE_DEBUG=false
```

#### **Step 5: Run Migration**
```bash
php artisan migrate
```

#### **Step 6: Clear Caches**
```bash
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

## 🔧 **Service Provider Registration**

Add this line to `config/app.php` in the `providers` array:
```php
'providers' => [
    // ... other providers
    Pterodactyl\Plugins\CloudflareDnsConfigurator\CloudflareDnsConfiguratorPlugin::class,
],
```

## 🧪 **Testing the Installation**

### **1. Check Plugin Loading**
```bash
# Check if the plugin is loaded
php artisan tinker
>>> app('Pterodactyl\Plugins\CloudflareDnsConfigurator\CloudflareDnsConfiguratorPlugin')
# Should return the plugin instance
```

### **2. Check Logs**
```bash
# Follow logs in real-time
tail -f storage/logs/laravel.log | grep "Cloudflare DNS Configurator"
# Should show: "Cloudflare DNS Configurator plugin booted successfully"
```

### **3. Test with Minecraft Server**
1. Create a Minecraft server in Pterodactyl
2. Check logs for plugin activity
3. Verify DNS records are created in Cloudflare

## 🐛 **Troubleshooting**

### **Plugin Not Loading**
- Check if files are copied to the correct location
- Verify composer dependencies are installed
- Check for PHP errors in logs

### **Configuration Not Found**
- Ensure `config/cloudflare.php` exists
- Check `.env` file has correct variables
- Run `php artisan config:clear`

### **Views Not Found**
- Verify views are copied to `resources/views/vendor/cloudflare-dns-configurator/`
- Run `php artisan view:clear`
- Check file permissions

### **Migration Issues**
- Ensure you're running from Pterodactyl root directory
- Check database connection
- Verify migration file exists

## 📱 **Adding Components to Panel**

Once installed, add these components to your server views:

### **Replace IP Display with DNS:**
```blade
@component('cloudflare-dns-configurator::components.server-connection-info', ['server' => $server])
@endcomponent
```

### **Add DNS Info Card:**
```blade
@component('cloudflare-dns-configurator::components.dns-info-card', ['server' => $server])
@endcomponent
```

### **Compact Display for Lists:**
```blade
@component('cloudflare-dns-configurator::components.server-connection-compact', ['server' => $server])
@endcomponent
```

## 🎯 **What Happens Next**

1. **Plugin loads** automatically when Pterodactyl starts
2. **DNS records created** when Minecraft servers are created
3. **DNS info displayed** in panel instead of IP addresses
4. **Records cleaned up** when servers are deleted

## 🆘 **Need Help?**

1. **Run the verification script**: `php install-verify.php`
2. **Check the logs**: `tail -f storage/logs/laravel.log`
3. **Verify configuration**: `php artisan config:show cloudflare`
4. **Test API connection**: Check Cloudflare dashboard

---

**That's it!** The manual installation bypasses all the publish tag issues. 🎉
