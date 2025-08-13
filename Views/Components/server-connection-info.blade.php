@if($dnsConfigured && $fullDnsName)
    <!-- DNS Connection Info - Primary Display -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">
                        Connect via DNS
                    </h3>
                    <p class="text-sm text-blue-600 dark:text-blue-300">
                        Use this domain name to connect to your server
                    </p>
                </div>
            </div>
            <div class="flex-shrink-0">
                <button 
                    type="button" 
                    class="inline-flex items-center px-4 py-2 border border-blue-300 dark:border-blue-600 shadow-sm text-sm leading-4 font-medium rounded-md text-blue-700 dark:text-blue-300 bg-white dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-blue-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                    onclick="navigator.clipboard.writeText('{{ $fullDnsName }}').then(() => { this.innerHTML = '<svg class=\'w-4 h-4 mr-2\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\'></path></svg>Copied!'; setTimeout(() => { this.innerHTML = '<svg class=\'w-4 h-4 mr-2\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z\'></path></svg>Copy DNS'; }, 2000); })"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Copy DNS
                </button>
            </div>
        </div>
        
        <div class="mt-4 bg-white dark:bg-gray-800 rounded-md p-4 border border-blue-100 dark:border-blue-600">
            <div class="text-center">
                <div class="text-2xl font-mono font-bold text-blue-900 dark:text-blue-100 break-all">
                    {{ $fullDnsName }}
                </div>
                <div class="mt-2 text-sm text-blue-600 dark:text-blue-300">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        DNS Active
                    </span>
                </div>
            </div>
            
            <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                <div class="text-center">
                    <div class="text-gray-500 dark:text-gray-400">Port</div>
                    <div class="font-medium text-gray-900 dark:text-gray-100">25565</div>
                </div>
                <div class="text-center">
                    <div class="text-gray-500 dark:text-gray-400">Type</div>
                    <div class="font-medium text-gray-900 dark:text-gray-100">Minecraft</div>
                </div>
            </div>
        </div>
        
        <!-- Fallback IP Info (Collapsible) -->
        @if($hasAllocation)
        <div class="mt-4">
            <details class="group">
                <summary class="flex items-center justify-between cursor-pointer text-sm text-blue-600 dark:text-blue-300 hover:text-blue-700 dark:hover:text-blue-200">
                    <span>Show IP Address (Fallback)</span>
                    <svg class="w-4 h-4 transform group-open:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </summary>
                <div class="mt-2 pt-2 border-t border-blue-100 dark:border-blue-600">
                    <div class="text-xs text-blue-500 dark:text-blue-400">
                        @if($server->allocation)
                            IP: {{ $server->allocation->ip }}:{{ $server->allocation->port }}
                        @else
                            @php
                                $allocation = $server->allocations()->first();
                            @endphp
                            @if($allocation)
                                IP: {{ $allocation->ip }}:{{ $allocation->port }}
                            @else
                                No IP allocation found
                            @endif
                        @endif
                    </div>
                </div>
            </details>
        </div>
        @endif
    </div>
@else
    <!-- Fallback to Standard IP Display -->
    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Server Connection
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    DNS not configured yet - using IP address
                </p>
            </div>
        </div>
        
        <div class="mt-4 bg-white dark:bg-gray-700 rounded-md p-4">
            @if($hasAllocation)
                @if($server->allocation)
                    <div class="text-center">
                        <div class="text-2xl font-mono font-bold text-gray-900 dark:text-gray-100">
                            {{ $server->allocation->ip }}:{{ $server->allocation->port }}
                        </div>
                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Direct IP Connection
                        </div>
                    </div>
                @else
                    @php
                        $allocation = $server->allocations()->first();
                    @endphp
                    @if($allocation)
                        <div class="text-center">
                            <div class="text-2xl font-mono font-bold text-gray-900 dark:text-gray-100">
                                {{ $allocation->ip }}:{{ $allocation->port }}
                            </div>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Direct IP Connection
                            </div>
                        </div>
                    @else
                        <div class="text-center text-gray-500 dark:text-gray-400">
                            No IP allocation found
                        </div>
                    @endif
                @endif
            @else
                <div class="text-center text-gray-500 dark:text-gray-400">
                    No IP allocation found
                </div>
            @endif
        </div>
        
        <div class="mt-4 text-center">
            <div class="text-xs text-gray-400 dark:text-gray-500">
                DNS will be automatically configured when the server is ready
            </div>
        </div>
    </div>
@endif
