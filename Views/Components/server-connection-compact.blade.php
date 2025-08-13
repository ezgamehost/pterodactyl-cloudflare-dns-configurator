@if($dnsConfigured && $fullDnsName)
    <!-- Compact DNS Connection Info -->
    <div class="flex items-center space-x-2">
        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
        </svg>
        <span class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ $fullDnsName }}</span>
        <button 
            type="button" 
            class="text-blue-500 hover:text-blue-700 dark:hover:text-blue-300"
            onclick="navigator.clipboard.writeText('{{ $fullDnsName }}').then(() => { this.innerHTML = '✓'; setTimeout(() => { this.innerHTML = '📋'; }, 2000); })"
            title="Copy DNS name"
        >
            📋
        </button>
    </div>
@else
    <!-- Fallback IP Display -->
    @if($hasAllocation)
        @if($server->allocation)
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                </svg>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $server->allocation->ip }}:{{ $server->allocation->port }}</span>
            </div>
        @else
            @php
                $allocation = $server->allocations()->first();
            @endphp
            @if($allocation)
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $allocation->ip }}:{{ $allocation->port }}</span>
                </div>
            @else
                <span class="text-sm text-gray-400 dark:text-gray-500">No connection info</span>
            @endif
        @endif
    @else
        <span class="text-sm text-gray-400 dark:text-gray-500">No connection info</span>
    @endif
@endif
