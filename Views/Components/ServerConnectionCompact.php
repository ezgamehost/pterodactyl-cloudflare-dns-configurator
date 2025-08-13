<?php

namespace Pterodactyl\Plugins\CloudflareDnsConfigurator\Views\Components;

use Illuminate\View\Component;
use Pterodactyl\Models\Server;

class ServerConnectionCompact extends Component
{
    public Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function render()
    {
        return view('cloudflare-dns-configurator::components.server-connection-compact', [
            'server' => $this->server,
            'dnsName' => $this->server->dns_name,
            'dnsConfigured' => $this->server->dns_configured,
            'fullDnsName' => $this->server->full_dns_name,
            'hasAllocation' => $this->server->allocation || $this->server->allocations()->exists(),
        ]);
    }
}
