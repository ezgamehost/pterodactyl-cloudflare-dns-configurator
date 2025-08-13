<?php

namespace Pterodactyl\Plugins\CloudflareDnsConfigurator\Views\Components;

use Illuminate\View\Component;
use Pterodactyl\Models\Server;

class DnsInfoCard extends Component
{
    public Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function render()
    {
        return view('cloudflare-dns-configurator::components.dns-info-card', [
            'server' => $this->server,
            'dnsName' => $this->server->dns_name,
            'dnsConfigured' => $this->server->dns_configured,
            'dnsLastUpdated' => $this->server->dns_last_updated,
            'fullDnsName' => $this->server->full_dns_name,
        ]);
    }
}
