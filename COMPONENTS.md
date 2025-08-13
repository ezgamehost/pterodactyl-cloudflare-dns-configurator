# Available Components

This document describes all the available components for displaying DNS information in your Pterodactyl panel.

## Component Overview

The plugin provides three main components for different use cases:

1. **Server Connection Info** - Replaces IP/port display with DNS
2. **DNS Info Card** - Shows detailed DNS configuration status
3. **Server Connection Compact** - Space-efficient display for lists

## 1. Server Connection Info Component

**Purpose**: Primary component that replaces the standard IP/port display with DNS information

**Usage**:
```blade
@component('cloudflare-dns-configurator::components.server-connection-info', ['server' => $server])
@endcomponent
```

**Features**:
- ✅ **Prominent DNS Display**: Large, easy-to-read DNS name
- ✅ **Copy Button**: One-click copy functionality
- ✅ **Port Information**: Shows Minecraft port (25565)
- ✅ **Server Type**: Indicates Minecraft server
- ✅ **Status Badge**: Green "DNS Active" indicator
- ✅ **IP Fallback**: Collapsible IP address information
- ✅ **Responsive Design**: Works on all screen sizes
- ✅ **Dark Mode Support**: Automatic theme adaptation

**Best For**: Main server details page, connection information section

**Screenshot Preview**:
```
┌─────────────────────────────────────────────────────────┐
│ 🌐 Connect via DNS                    [Copy DNS]       │
│    Use this domain name to connect to your server      │
│                                                         │
│    ┌─────────────────────────────────────────────────┐ │
│    │           minecraft-server.example.com          │ │
│    │              [✓ DNS Active]                     │ │
│    │                                                 │ │
│    │    Port: 25565    Type: Minecraft               │ │
│    └─────────────────────────────────────────────────┘ │
│                                                         │
│    [Show IP Address (Fallback) ▼]                     │
└─────────────────────────────────────────────────────────┘
```

## 2. DNS Info Card Component

**Purpose**: Additional DNS configuration information and status

**Usage**:
```blade
@component('cloudflare-dns-configurator::components.dns-info-card', ['server' => $server])
@endcomponent
```

**Features**:
- ✅ **Configuration Status**: Shows if DNS is properly configured
- ✅ **Last Updated**: Timestamp of last DNS modification
- ✅ **Copy Functionality**: Easy DNS name copying
- ✅ **Record Details**: Shows A record type and status
- ✅ **Fallback Display**: Shows when DNS is not configured

**Best For**: Server details page, DNS configuration section

**Screenshot Preview**:
```
┌─────────────────────────────────────────────────────────┐
│ ✓ DNS Configuration                    Last updated     │
│    Cloudflare DNS automatically configured              │
│                                                         │
│    ┌─────────────────────────────────────────────────┐ │
│    │ Server DNS Name                                 │ │
│    │ minecraft-server.example.com        [Copy]      │ │
│    │                                                 │ │
│    │ Type: A Record    Status: Active                │ │
│    └─────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────┘
```

## 3. Server Connection Compact Component

**Purpose**: Space-efficient DNS display for lists and overviews

**Usage**:
```blade
@component('cloudflare-dns-configurator::components.server-connection-compact', ['server' => $server])
@endcomponent
```

**Features**:
- ✅ **Compact Design**: Minimal space usage
- ✅ **Quick Copy**: Simple copy functionality
- ✅ **IP Fallback**: Shows IP when DNS not configured
- ✅ **Icon Indicators**: Visual DNS vs IP distinction

**Best For**: Server lists, overviews, tables, compact displays

**Screenshot Preview**:
```
🌐 minecraft-server.example.com 📋
```

## Component Selection Guide

### For Main Server Page
Use **Server Connection Info** component to replace the standard IP/port display:

```blade
<!-- Replace this: -->
<div>IP: 192.168.1.100:25565</div>

<!-- With this: -->
@component('cloudflare-dns-configurator::components.server-connection-info', ['server' => $server])
@endcomponent
```

### For Additional DNS Information
Use **DNS Info Card** component in a separate section:

```blade
<div class="mt-6">
    <h3>DNS Configuration</h3>
    @component('cloudflare-dns-configurator::components.dns-info-card', ['server' => $server])
    @endcomponent
</div>
```

### For Server Lists
Use **Server Connection Compact** component in tables or lists:

```blade
@foreach($servers as $server)
    <tr>
        <td>{{ $server->name }}</td>
        <td>
            @component('cloudflare-dns-configurator::components.server-connection-compact', ['server' => $server])
            @endcomponent
        </td>
        <td>{{ $server->status }}</td>
    </tr>
@endforeach
```

## Component Behavior

### When DNS is Configured
- **Server Connection Info**: Shows DNS name prominently with copy button
- **DNS Info Card**: Shows configuration status and last updated time
- **Compact**: Shows DNS name with copy icon

### When DNS is Not Configured
- **Server Connection Info**: Shows IP address with "DNS not configured" message
- **DNS Info Card**: Shows warning that DNS will be configured automatically
- **Compact**: Shows IP address with globe icon

### Fallback Information
All components gracefully fall back to showing IP address information when DNS is not available, ensuring users always have connection information.

## Customization

### Styling
All components use Tailwind CSS classes and can be customized by:
1. Publishing the views: `php artisan vendor:publish --tag=cloudflare-dns-configurator-views`
2. Editing the published views in `resources/views/vendor/cloudflare-dns-configurator/components/`

### Content
Modify component behavior by editing the component classes in:
- `ptero-plugin/Views/Components/ServerConnectionInfo.php`
- `ptero-plugin/Views/Components/DnsInfoCard.php`
- `ptero-plugin/Views/Components/ServerConnectionCompact.php`

## Integration Examples

### With WHMCS Integration
```blade
<!-- Show DNS info in WHMCS client area -->
@if($server->dns_configured)
    <div class="alert alert-info">
        <strong>Server Domain:</strong> {{ $server->full_dns_name }}
        <button onclick="copyToClipboard('{{ $server->full_dns_name }}')">Copy</button>
    </div>
@endif
```

### With Custom Themes
```blade
<!-- Integrate with custom theme styling -->
<div class="custom-dns-section">
    @component('cloudflare-dns-configurator::components.server-connection-info', ['server' => $server])
    @endcomponent
</div>
```

### With API Responses
The components can also be used in API responses by rendering them to HTML:

```php
$dnsInfo = view('cloudflare-dns-configurator::components.server-connection-info', [
    'server' => $server
])->render();

return response()->json([
    'server' => $server,
    'dns_info_html' => $dnsInfo
]);
```

## Troubleshooting

### Component Not Displaying
1. Check if the server has DNS records configured
2. Verify the ViewServiceProvider is registered
3. Clear view cache: `php artisan view:clear`

### Styling Issues
1. Ensure Tailwind CSS is loaded
2. Check for CSS conflicts with your theme
3. Verify component CSS classes are not overridden

### Performance
- Components are lightweight and optimized
- No additional database queries
- Cached view components for better performance
