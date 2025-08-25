<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigifazzService
{
    /**
     * Digiflazz API base URL.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Digiflazz username.
     *
     * @var string
     */
    protected $username;

    /**
     * Digiflazz API key.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Create a new service instance.
     */
    public function __construct()
    {
        $this->baseUrl = Setting::get('digiflazz_base_url', 'https://api.digiflazz.com/v1');
        $this->username = Setting::get('digiflazz_username', '');
        $this->apiKey = Setting::get('digiflazz_api_key', '');
    }

    /**
     * Get balance from Digiflazz.
     *
     * @return array
     */
    public function getBalance(): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/cek-saldo', [
                'cmd' => 'deposit',
                'username' => $this->username,
                'sign' => $this->generateSignature('depo'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'balance' => $data['data']['deposit'] ?? 0,
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to fetch balance',
                'balance' => 0,
            ];
        } catch (\Exception $e) {
            Log::error('Digiflazz get balance error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'balance' => 0,
            ];
        }
    }

    /**
     * Get product price list from Digiflazz.
     *
     * @return array
     */
    public function getPriceList(): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/price-list', [
                'cmd' => 'prepaid',
                'username' => $this->username,
                'sign' => $this->generateSignature('pricelist'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to fetch price list',
                'data' => [],
            ];
        } catch (\Exception $e) {
            Log::error('Digiflazz get price list error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Process order with Digiflazz.
     *
     * @param Order $order
     * @return array
     */
    public function processOrder(Order $order): array
    {
        try {
            $refId = 'GTU' . $order->id . time();
            
            $payload = [
                'username' => $this->username,
                'buyer_sku_code' => $order->product->digiflazz_code,
                'customer_no' => $order->game_id,
                'ref_id' => $refId,
                'sign' => $this->generateSignature($refId),
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transaction', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['data']['status']) && $data['data']['status'] === 'Sukses') {
                    return [
                        'success' => true,
                        'data' => $data['data'],
                        'message' => 'Transaction successful',
                    ];
                } else {
                    return [
                        'success' => false,
                        'data' => $data['data'] ?? null,
                        'message' => $data['data']['message'] ?? 'Transaction failed',
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to process transaction',
                'data' => null,
            ];
        } catch (\Exception $e) {
            Log::error('Digiflazz process order error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Check game ID validity.
     *
     * @param string $gameCode
     * @param string $gameId
     * @return array
     */
    public function checkGameId(string $gameCode, string $gameId): array
    {
        // This is a placeholder - implement based on Digiflazz API
        // Some games may have specific endpoints for ID validation
        
        return [
            'success' => true,
            'valid' => true,
            'player_name' => 'Player Name', // If available from API
        ];
    }

    /**
     * Generate signature for Digiflazz API.
     *
     * @param string $refId
     * @return string
     */
    protected function generateSignature(string $refId): string
    {
        return hash('sha256', $this->username . $this->apiKey . $refId);
    }
}