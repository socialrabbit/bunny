<?php

namespace Bunny\Services;

use Illuminate\Support\Facades\{
    Cache,
    Log,
    Http,
    Queue,
    Config
};
use Illuminate\Support\Str;
use Bunny\Models\{
    Integration,
    IntegrationLog,
    Webhook,
    ApiCredential,
    IntegrationSetting
};
use Bunny\Events\{
    IntegrationEvent,
    WebhookReceived,
    ApiCallMade
};

class IntegrationService
{
    protected $cache;
    protected $settings;

    public function __construct()
    {
        $this->cache = Cache::tags(['integration']);
        $this->settings = $this->loadIntegrationSettings();
    }

    /**
     * Initialize integration service
     */
    public function initialize()
    {
        $this->setupIntegrations();
        $this->setupWebhooks();
        $this->setupApiCredentials();
        $this->setupEventListeners();
    }

    /**
     * Load integration settings
     */
    protected function loadIntegrationSettings()
    {
        return $this->cache->remember('integration.settings', 3600, function () {
            return IntegrationSetting::first() ?? $this->createDefaultSettings();
        });
    }

    /**
     * Create default integration settings
     */
    protected function createDefaultSettings()
    {
        return IntegrationSetting::create([
            'enable_webhooks' => true,
            'webhook_secret' => Str::random(32),
            'webhook_timeout' => 30,
            'max_webhook_retries' => 3,
            'enable_api_rate_limiting' => true,
            'api_rate_limit' => 60,
            'api_rate_limit_window' => 1,
            'enable_api_logging' => true,
            'enable_error_notifications' => true,
            'default_timeout' => 30,
            'retry_attempts' => 3,
            'retry_delay' => 5,
        ]);
    }

    /**
     * Setup integrations
     */
    protected function setupIntegrations()
    {
        $integrations = Integration::where('is_active', true)->get();

        foreach ($integrations as $integration) {
            $this->setupIntegration($integration);
        }
    }

    /**
     * Setup webhooks
     */
    protected function setupWebhooks()
    {
        $webhooks = Webhook::where('is_active', true)->get();

        foreach ($webhooks as $webhook) {
            $this->setupWebhook($webhook);
        }
    }

    /**
     * Setup API credentials
     */
    protected function setupApiCredentials()
    {
        $credentials = ApiCredential::where('is_active', true)->get();

        foreach ($credentials as $credential) {
            $this->setupApiCredential($credential);
        }
    }

    /**
     * Setup event listeners
     */
    protected function setupEventListeners()
    {
        // Implement event listener setup
    }

    /**
     * Setup integration
     */
    protected function setupIntegration($integration)
    {
        // Implement integration setup
    }

    /**
     * Setup webhook
     */
    protected function setupWebhook($webhook)
    {
        // Implement webhook setup
    }

    /**
     * Setup API credential
     */
    protected function setupApiCredential($credential)
    {
        // Implement API credential setup
    }

    /**
     * Register integration
     */
    public function registerIntegration(array $data)
    {
        $integration = Integration::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'provider' => $data['provider'],
            'config' => $data['config'],
            'is_active' => $data['is_active'] ?? true,
            'webhook_url' => $data['webhook_url'] ?? null,
            'api_key' => $data['api_key'] ?? null,
            'api_secret' => $data['api_secret'] ?? null,
            'access_token' => $data['access_token'] ?? null,
            'refresh_token' => $data['refresh_token'] ?? null,
            'token_expires_at' => $data['token_expires_at'] ?? null,
        ]);

        $this->setupIntegration($integration);
        return $integration;
    }

    /**
     * Register webhook
     */
    public function registerWebhook(array $data)
    {
        $webhook = Webhook::create([
            'integration_id' => $data['integration_id'],
            'event' => $data['event'],
            'url' => $data['url'],
            'method' => $data['method'] ?? 'POST',
            'headers' => $data['headers'] ?? [],
            'payload' => $data['payload'] ?? [],
            'is_active' => $data['is_active'] ?? true,
            'retry_count' => 0,
            'last_triggered_at' => null,
        ]);

        $this->setupWebhook($webhook);
        return $webhook;
    }

    /**
     * Register API credential
     */
    public function registerApiCredential(array $data)
    {
        $credential = ApiCredential::create([
            'integration_id' => $data['integration_id'],
            'name' => $data['name'],
            'type' => $data['type'],
            'key' => $data['key'],
            'secret' => $data['secret'],
            'is_active' => $data['is_active'] ?? true,
            'expires_at' => $data['expires_at'] ?? null,
            'last_used_at' => null,
        ]);

        $this->setupApiCredential($credential);
        return $credential;
    }

    /**
     * Make API request
     */
    public function makeApiRequest($integration, $endpoint, $method = 'GET', $data = [], $headers = [])
    {
        $credential = $integration->apiCredential;
        
        if (!$credential || !$credential->is_active) {
            throw new \Exception('No active API credential found');
        }

        $headers = array_merge($headers, [
            'Authorization' => "Bearer {$credential->key}",
            'Content-Type' => 'application/json',
        ]);

        try {
            $response = Http::withHeaders($headers)
                ->timeout($this->settings->default_timeout)
                ->retry($this->settings->retry_attempts, $this->settings->retry_delay)
                ->$method($endpoint, $data);

            $this->logApiCall($integration, $endpoint, $method, $data, $response);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception($response->body());
        } catch (\Exception $e) {
            $this->handleApiError($integration, $e);
            throw $e;
        }
    }

    /**
     * Handle webhook
     */
    public function handleWebhook($webhook, $payload)
    {
        if (!$webhook->is_active) {
            return false;
        }

        try {
            $response = Http::withHeaders($webhook->headers)
                ->timeout($this->settings->webhook_timeout)
                ->retry($this->settings->max_webhook_retries)
                ->$webhook->method($webhook->url, $payload);

            $webhook->update([
                'last_triggered_at' => now(),
                'retry_count' => 0,
            ]);

            event(new WebhookReceived($webhook, $payload));

            return $response->successful();
        } catch (\Exception $e) {
            $this->handleWebhookError($webhook, $e);
            return false;
        }
    }

    /**
     * Log API call
     */
    protected function logApiCall($integration, $endpoint, $method, $data, $response)
    {
        if (!$this->settings->enable_api_logging) {
            return;
        }

        IntegrationLog::create([
            'integration_id' => $integration->id,
            'type' => 'api_call',
            'endpoint' => $endpoint,
            'method' => $method,
            'request_data' => $data,
            'response_data' => $response->json(),
            'status_code' => $response->status(),
            'duration' => $response->handlerStats()['total_time'] ?? 0,
        ]);

        event(new ApiCallMade($integration, $endpoint, $method, $response));
    }

    /**
     * Handle API error
     */
    protected function handleApiError($integration, $error)
    {
        Log::error("API Error for integration {$integration->name}: {$error->getMessage()}");

        if ($this->settings->enable_error_notifications) {
            // Send error notification
        }
    }

    /**
     * Handle webhook error
     */
    protected function handleWebhookError($webhook, $error)
    {
        Log::error("Webhook Error for {$webhook->event}: {$error->getMessage()}");

        $webhook->increment('retry_count');

        if ($webhook->retry_count >= $this->settings->max_webhook_retries) {
            $webhook->update(['is_active' => false]);
        }

        if ($this->settings->enable_error_notifications) {
            // Send error notification
        }
    }

    /**
     * Refresh integration token
     */
    public function refreshIntegrationToken($integration)
    {
        if (!$integration->refresh_token) {
            return false;
        }

        try {
            $response = Http::post($integration->provider->token_url, [
                'grant_type' => 'refresh_token',
                'refresh_token' => $integration->refresh_token,
                'client_id' => $integration->api_key,
                'client_secret' => $integration->api_secret,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $integration->update([
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'] ?? $integration->refresh_token,
                    'token_expires_at' => now()->addSeconds($data['expires_in']),
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Token refresh failed for integration {$integration->name}: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Get integration status
     */
    public function getIntegrationStatus($integration)
    {
        return [
            'is_active' => $integration->is_active,
            'last_sync' => $integration->last_sync_at,
            'webhook_status' => $this->getWebhookStatus($integration),
            'api_status' => $this->getApiStatus($integration),
            'error_count' => $integration->error_count,
            'last_error' => $integration->last_error,
        ];
    }

    /**
     * Get webhook status
     */
    protected function getWebhookStatus($integration)
    {
        $webhooks = $integration->webhooks()
            ->where('is_active', true)
            ->get();

        return [
            'total' => $webhooks->count(),
            'active' => $webhooks->where('retry_count', 0)->count(),
            'failed' => $webhooks->where('retry_count', '>', 0)->count(),
            'last_triggered' => $webhooks->max('last_triggered_at'),
        ];
    }

    /**
     * Get API status
     */
    protected function getApiStatus($integration)
    {
        $credential = $integration->apiCredential;

        return [
            'is_active' => $credential && $credential->is_active,
            'last_used' => $credential ? $credential->last_used_at : null,
            'expires_at' => $credential ? $credential->expires_at : null,
            'error_count' => $integration->error_count,
        ];
    }

    /**
     * Get integration logs
     */
    public function getIntegrationLogs($integration, $limit = 100)
    {
        return IntegrationLog::where('integration_id', $integration->id)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get webhook logs
     */
    public function getWebhookLogs($webhook, $limit = 100)
    {
        return IntegrationLog::where('webhook_id', $webhook->id)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get API logs
     */
    public function getApiLogs($credential, $limit = 100)
    {
        return IntegrationLog::where('api_credential_id', $credential->id)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Test integration
     */
    public function testIntegration($integration)
    {
        try {
            // Test API connection
            $apiTest = $this->testApiConnection($integration);

            // Test webhook
            $webhookTest = $this->testWebhook($integration);

            return [
                'success' => $apiTest && $webhookTest,
                'api_test' => $apiTest,
                'webhook_test' => $webhookTest,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Test API connection
     */
    protected function testApiConnection($integration)
    {
        try {
            $response = $this->makeApiRequest($integration, '/test');
            return $response['success'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Test webhook
     */
    protected function testWebhook($integration)
    {
        try {
            $webhook = $integration->webhooks()->first();
            if (!$webhook) {
                return true;
            }

            $response = Http::post($webhook->url, [
                'event' => 'test',
                'timestamp' => now(),
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
} 