<?php

namespace Pterodactyl\Plugins\CloudflareDnsConfigurator\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class CloudflareDnsService
{
    /**
     * Cloudflare API token.
     */
    private string $apiToken;

    /**
     * Cloudflare zone ID.
     */
    private string $zoneId;

    /**
     * Cloudflare API base URL.
     */
    private const API_BASE_URL = 'https://api.cloudflare.com/client/v4';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->apiToken = config('cloudflare.api_token');
        $this->zoneId = config('cloudflare.zone_id');
    }

    /**
     * Create an A record in Cloudflare.
     */
    public function createARecord(string $name, string $ipAddress, bool $proxied = true): bool
    {
        Log::info("CloudflareDnsService - Creating A record", [
            'name' => $name,
            'ip_address' => $ipAddress,
            'proxied' => $proxied,
            'zone_id' => $this->zoneId,
            'api_base_url' => self::API_BASE_URL,
        ]);

        try {
            $payload = [
                'type' => 'A',
                'name' => $name,
                'content' => $ipAddress,
                'ttl' => config('cloudflare.default_ttl', 1),
                'proxied' => $proxied,
            ];

            Log::info("CloudflareDnsService - API request payload", [
                'payload' => $payload,
                'endpoint' => "/zones/{$this->zoneId}/dns_records",
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post(self::API_BASE_URL . "/zones/{$this->zoneId}/dns_records", $payload);

            Log::info("CloudflareDnsService - API response received", [
                'status_code' => $response->status(),
                'response_headers' => $response->headers(),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info("CloudflareDnsService - A record created successfully", [
                    'name' => $name,
                    'ip_address' => $ipAddress,
                    'response_data' => $responseData,
                ]);
                return true;
            }

            $responseData = $response->json();
            Log::error('CloudflareDnsService - Failed to create A record', [
                'name' => $name,
                'ip_address' => $ipAddress,
                'status_code' => $response->status(),
                'response_data' => $responseData,
                'response_headers' => $response->headers(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('CloudflareDnsService - Exception while creating A record', [
                'name' => $name,
                'ip_address' => $ipAddress,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Create a Minecraft-compliant SRV record in Cloudflare.
     * Minecraft SRV records follow the format: _minecraft._tcp.subdomain
     */
    public function createMinecraftSrvRecord(string $name, string $ipAddress, int $port = 25565, bool $proxied = false): bool
    {
        Log::info("CloudflareDnsService - Creating Minecraft SRV record", [
            'name' => $name,
            'ip_address' => $ipAddress,
            'port' => $port,
            'proxied' => $proxied,
            'zone_id' => $this->zoneId,
        ]);

        try {
            // First create the A record for the IP address
            $aRecordName = $name . '-direct'; // Use a subdomain for the A record
            $aRecordSuccess = $this->createARecord($aRecordName, $ipAddress, $proxied);
            
            if (!$aRecordSuccess) {
                Log::error("CloudflareDnsService - Failed to create A record for SRV record", [
                    'name' => $aRecordName,
                    'ip_address' => $ipAddress,
                ]);
                return false;
            }

            // Now create the SRV record pointing to the A record
            $srvPayload = [
                'type' => 'SRV',
                'name' => $name,
                'data' => [
                    'service' => '_minecraft',
                    'proto' => '_tcp',
                    'name' => $aRecordName,
                    'priority' => 0,
                    'weight' => 5,
                    'port' => $port,
                    'target' => $aRecordName . '.' . config('cloudflare.domain_suffix', ''),
                ],
                'ttl' => config('cloudflare.default_ttl', 1),
                'proxied' => $proxied,
            ];

            Log::info("CloudflareDnsService - SRV record payload", [
                'payload' => $srvPayload,
                'endpoint' => "/zones/{$this->zoneId}/dns_records",
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post(self::API_BASE_URL . "/zones/{$this->zoneId}/dns_records", $srvPayload);

            Log::info("CloudflareDnsService - SRV API response received", [
                'status_code' => $response->status(),
                'response_headers' => $response->headers(),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info("CloudflareDnsService - Minecraft SRV record created successfully", [
                    'name' => $name,
                    'ip_address' => $ipAddress,
                    'port' => $port,
                    'a_record_name' => $aRecordName,
                    'response_data' => $responseData,
                ]);
                return true;
            }

            $responseData = $response->json();
            Log::error('CloudflareDnsService - Failed to create Minecraft SRV record', [
                'name' => $name,
                'ip_address' => $ipAddress,
                'port' => $port,
                'status_code' => $response->status(),
                'response_data' => $responseData,
                'response_headers' => $response->headers(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('CloudflareDnsService - Exception while creating Minecraft SRV record', [
                'name' => $name,
                'ip_address' => $ipAddress,
                'port' => $port,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Delete an A record from Cloudflare.
     */
    public function deleteARecord(string $name, string $ipAddress): bool
    {
        Log::info("CloudflareDnsService - Deleting A record", [
            'name' => $name,
            'ip_address' => $ipAddress,
            'zone_id' => $this->zoneId,
        ]);

        try {
            // First, find the record ID
            Log::info("CloudflareDnsService - Searching for record ID", [
                'name' => $name,
                'ip_address' => $ipAddress,
            ]);

            $recordId = $this->findRecordId($name, $ipAddress);

            if (!$recordId) {
                Log::warning("CloudflareDnsService - Record not found for deletion", [
                    'name' => $name,
                    'ip_address' => $ipAddress,
                ]);
                return false;
            }

            Log::info("CloudflareDnsService - Record ID found", [
                'name' => $name,
                'ip_address' => $ipAddress,
                'record_id' => $recordId,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->delete(self::API_BASE_URL . "/zones/{$this->zoneId}/dns_records/{$recordId}");

            Log::info("CloudflareDnsService - Delete API response received", [
                'status_code' => $response->status(),
                'response_headers' => $response->headers(),
            ]);

            if ($response->successful()) {
                Log::info("CloudflareDnsService - A record deleted successfully", [
                    'name' => $name,
                    'ip_address' => $ipAddress,
                    'record_id' => $recordId,
                ]);
                return true;
            }

            $responseData = $response->json();
            Log::error('CloudflareDnsService - Failed to delete A record', [
                'name' => $name,
                'ip_address' => $ipAddress,
                'record_id' => $recordId,
                'status_code' => $response->status(),
                'response_data' => $responseData,
                'response_headers' => $response->headers(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('CloudflareDnsService - Exception while deleting A record', [
                'name' => $name,
                'ip_address' => $ipAddress,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Delete a Minecraft SRV record and its associated A record from Cloudflare.
     */
    public function deleteMinecraftSrvRecord(string $name, string $ipAddress, int $port = 25565): bool
    {
        Log::info("CloudflareDnsService - Deleting Minecraft SRV record", [
            'name' => $name,
            'ip_address' => $ipAddress,
            'port' => $port,
            'zone_id' => $this->zoneId,
        ]);

        try {
            $aRecordName = $name . '-direct';
            $success = true;

            // First delete the SRV record
            Log::info("CloudflareDnsService - Searching for SRV record ID", [
                'name' => $name,
            ]);

            $srvRecordId = $this->findSrvRecordId($name);

            if ($srvRecordId) {
                Log::info("CloudflareDnsService - SRV record ID found", [
                    'name' => $name,
                    'record_id' => $srvRecordId,
                ]);

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiToken,
                ])->delete(self::API_BASE_URL . "/zones/{$this->zoneId}/dns_records/{$srvRecordId}");

                if ($response->successful()) {
                    Log::info("CloudflareDnsService - SRV record deleted successfully", [
                        'name' => $name,
                        'record_id' => $srvRecordId,
                    ]);
                } else {
                    Log::error("CloudflareDnsService - Failed to delete SRV record", [
                        'name' => $name,
                        'record_id' => $srvRecordId,
                        'status_code' => $response->status(),
                    ]);
                    $success = false;
                }
            } else {
                Log::warning("CloudflareDnsService - SRV record not found for deletion", [
                    'name' => $name,
                ]);
            }

            // Then delete the associated A record
            $aRecordSuccess = $this->deleteARecord($aRecordName, $ipAddress);
            if (!$aRecordSuccess) {
                $success = false;
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('CloudflareDnsService - Exception while deleting Minecraft SRV record', [
                'name' => $name,
                'ip_address' => $ipAddress,
                'port' => $port,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Find a record ID by name and content.
     */
    private function findRecordId(string $name, string $ipAddress): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->get(self::API_BASE_URL . "/zones/{$this->zoneId}/dns_records", [
                'type' => 'A',
                'name' => $name,
                'content' => $ipAddress,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['result'])) {
                    return $data['result'][0]['id'];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Exception while finding record ID', [
                'name' => $name,
                'ip' => $ipAddress,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Find an SRV record ID by name.
     */
    private function findSrvRecordId(string $name): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->get(self::API_BASE_URL . "/zones/{$this->zoneId}/dns_records", [
                'type' => 'SRV',
                'name' => $name,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['result'])) {
                    return $data['result'][0]['id'];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Exception while finding SRV record ID', [
                'name' => $name,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Check if the service is properly configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiToken) && !empty($this->zoneId);
    }

    /**
     * Get the current zone ID.
     */
    public function getZoneId(): string
    {
        return $this->zoneId;
    }
}
