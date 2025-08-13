# Installation Guide for DNS Info Card

This guide explains how to add the DNS information card to your Pterodactyl panel.

## 1. Run Database Migration

First, run the database migration to add the DNS fields:

```bash
php artisan migrate
```

## 2. Publish Views

Publish the view components to your panel:

```bash
php artisan vendor:publish --tag=cloudflare-dns-configurator-views
```

## 3. Add DNS Components to Your Panel

### Option A: Replace Server Connection Info (Recommended)

To show DNS names instead of IP addresses, replace the standard connection info with:

```blade
@component('cloudflare-dns-configurator::components.server-connection-info', ['server' => $server])
@endcomponent
```

This will display the DNS name prominently instead of the IP address.

### Option B: Add DNS Info Card

Add the DNS info card to your server details view template. Look for a file like:
- `resources/views/server/index.blade.php` (Pterodactyl 1.x)
- `resources/views/templates/server.blade.php` (Custom themes)

Add this line where you want the DNS info to appear:

```blade
@component('cloudflare-dns-configurator::components/dns-info-card', ['server' => $server])
@endcomponent
```

### Option C: Compact DNS Display for Lists

For server lists and overviews, use the compact component:

```blade
@component('cloudflare-dns-configurator::components/server-connection-compact', ['server' => $server])
@endcomponent
```

## 4. Component Types

### Server Connection Info Component
**Purpose**: Replaces the standard IP/port display with DNS information
**Use Case**: Main server details page, connection information section
**Features**: 
- Prominently displays DNS name
- Shows port and server type
- Includes copy button
- Collapsible IP fallback information

### DNS Info Card Component
**Purpose**: Additional DNS information display
**Use Case**: Server details page, DNS configuration section
**Features**:
- Shows DNS configuration status
- Last updated timestamp
- Configuration details

### Compact Component
**Purpose**: Space-efficient DNS display
**Use Case**: Server lists, overviews, tables
**Features**:
- Small footprint
- Quick copy functionality
- Fallback to IP when DNS not configured

## 5. Example Placement

Here's an example of where to place the components in a typical server details page:

```blade
<!-- Server Connection Section -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
            Server Connection
        </h3>
        
        <!-- Replace standard IP/port display with DNS info -->
        @component('cloudflare-dns-configurator::components.server-connection-info', ['server' => $server])
        @endcomponent
        
        <!-- More connection info here -->
    </div>
</div>

<!-- Server Information Section -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
            Server Information
        </h3>
        
        <!-- Existing server info here -->
        
        <!-- Add DNS Info Card here -->
        @component('cloudflare-dns-configurator::components/dns-info-card', ['server' => $server])
        @endcomponent
        
        <!-- More server info here -->
    </div>
</div>
```

## 6. Customization

### Styling
The component uses Tailwind CSS classes. You can customize the appearance by:
1. Publishing the views: `php artisan vendor:publish --tag=cloudflare-dns-configurator-views`
2. Editing the published view at `resources/views/vendor/cloudflare-dns-configurator/components/dns-info-card.blade.php`

### Content
You can modify what information is displayed by editing the view component class:
- `ptero-plugin/Views/Components/DnsInfoCard.php`

## 7. Troubleshooting

### DNS Info Not Showing
1. Check if the server has DNS records configured
2. Verify the database migration ran successfully
3. Check the logs for any errors

### Component Not Found
1. Ensure the ViewServiceProvider is registered
2. Clear view cache: `php artisan view:clear`
3. Verify the component is published correctly

### Styling Issues
1. Make sure Tailwind CSS is properly loaded
2. Check if your theme overrides the component styles
3. Verify the component is using the correct CSS classes for your theme

## 8. Advanced Usage

### Multiple DNS Records
If you need to display multiple DNS records, you can modify the component to show them in a list format.

### Custom Fields
You can extend the component to show additional information like:
- DNS record type (A, SRV, etc.)
- TTL values
- Proxy status
- Last DNS check

### Integration with Other Systems
The component can be easily integrated with:
- WHMCS integration
- Billing systems
- Monitoring tools
- Custom dashboards

## Support

If you encounter issues:
1. Check the Pterodactyl logs
2. Verify your Cloudflare configuration
3. Ensure all migrations have run successfully
4. Check that the service providers are properly registered
