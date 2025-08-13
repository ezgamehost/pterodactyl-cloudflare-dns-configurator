@if($dnsConfigured && $fullDnsName)
<div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    DNS Configuration
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Cloudflare DNS automatically configured
                </p>
            </div>
        </div>
        <div class="text-right">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Last updated
            </div>
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ $dnsLastUpdated ? $dnsLastUpdated->diffForHumans() : 'Unknown' }}
            </div>
        </div>
    </div>
    
    <div class="mt-4 bg-white dark:bg-gray-700 rounded-md p-3">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Server DNS Name
                </div>
                <div class="text-lg font-mono text-gray-900 dark:text-gray-100 break-all">
                    {{ $fullDnsName }}
                </div>
            </div>
            <div class="flex-shrink-0 ml-4">
                <button 
                    type="button" 
                    class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    onclick="navigator.clipboard.writeText('{{ $fullDnsName }}').then(() => { this.textContent = 'Copied!'; setTimeout(() => { this.textContent = 'Copy'; }, 2000); })"
                >
                    Copy
                </button>
            </div>
        </div>
        
        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Type: A Record</span>
                </div>
                <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Status: Active</span>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-4">
    <div class="flex items-center space-x-3">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                DNS Configuration
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                No DNS record configured yet
            </p>
        </div>
    </div>
    
    <div class="mt-3 bg-white dark:bg-gray-700 rounded-md p-3">
        <div class="text-sm text-gray-500 dark:text-gray-400">
            DNS records will be automatically created when the server is fully configured and running.
        </div>
    </div>
</div>
@endif
