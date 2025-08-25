<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PaymentService
{
    /**
     * Tokopay API key.
     *
     * @var string
     */
    protected $tokopayApiKey;

    /**
     * Tokopay merchant ID.
     *
     * @var string
     */
    protected $tokopayMerchantId;

    /**
     * Tokopay secret key.
     *
     * @var string
     */
    protected $tokopaySecret;

    /**
     * Tokopay API base URL.
     *
     * @var string
     */
    protected $tokopayBaseUrl;

    /**
     * Create a new service instance.
     */
    public function __construct()
    {
        $this->tokopayApiKey = Setting::get('tokopay_api_key', '');
        $this->tokopayMerchantId = Setting::get('tokopay_merchant_id', '');
        $this->tokopaySecret = Setting::get('tokopay_secret', '');
        $this->tokopayBaseUrl = Setting::get('tokopay_base_url', 'https://api.tokopay.id');
    }

    /**
     * Create payment for order.
     *
     * @param Order $order
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function createPayment(Order $order)
    {
        try {
            $paymentMethods = $this->getAvailablePaymentMethods();
            
            return Inertia::render('checkout', [
                'order' => $order->load('product'),
                'paymentMethods' => $paymentMethods,
            ]);
        } catch (\Exception $e) {
            Log::error('Payment creation error: ' . $e->getMessage());
            
            return back()->withErrors(['payment' => 'Unable to process payment. Please try again.']);
        }
    }

    /**
     * Process payment with Tokopay.
     *
     * @param Order $order
     * @param string $paymentMethod
     * @return array
     */
    public function processPayment(Order $order, string $paymentMethod): array
    {
        try {
            $payload = [
                'merchant_id' => $this->tokopayMerchantId,
                'kode_channel' => $paymentMethod,
                'reff_id' => $order->order_number,
                'amount' => $order->total_amount,
                'customer_name' => $order->user->name ?? 'Guest',
                'customer_email' => $order->customer_email,
                'customer_phone' => $order->customer_whatsapp,
                'redirect_url' => route('orders.success', $order),
                'expired_ts' => now()->addHours(24)->timestamp,
                'signature' => $this->generateTokopaySignature($order->order_number, $order->total_amount),
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->tokopayApiKey,
                'Content-Type' => 'application/json',
            ])->post($this->tokopayBaseUrl . '/v1/order', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'Success') {
                    $order->update([
                        'payment_method' => $paymentMethod,
                        'payment_reference' => $data['data']['trx_id'],
                    ]);

                    return [
                        'success' => true,
                        'payment_url' => $data['data']['pay_url'],
                        'payment_code' => $data['data']['pay_code'] ?? null,
                        'qr_code' => $data['data']['qr_string'] ?? null,
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to create payment',
            ];
        } catch (\Exception $e) {
            Log::error('Tokopay payment error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle payment callback from Tokopay.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleCallback(Request $request)
    {
        try {
            $signature = $request->header('X-Callback-Signature');
            $payload = $request->getContent();
            
            // Verify signature
            $expectedSignature = hash_hmac('sha256', $payload, $this->tokopaySecret);
            
            if (!hash_equals($expectedSignature, $signature)) {
                Log::warning('Invalid payment callback signature');
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
            }

            $data = $request->json()->all();
            $order = Order::where('order_number', $data['reff_id'])->first();

            if (!$order) {
                Log::warning('Order not found for callback: ' . $data['reff_id']);
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            // Update order status based on payment status
            if ($data['status'] === 'Success') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                ]);

                // If user exists, add to balance for future purchases
                if ($order->user) {
                    $order->user->addBalance($order->total_amount);
                }

                // Process with Digiflazz
                app(DigifazzService::class)->processOrder($order);
            } elseif ($data['status'] === 'Failed') {
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'failed',
                ]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Payment callback error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Callback processing failed'], 500);
        }
    }

    /**
     * Get available payment methods.
     *
     * @return array
     */
    protected function getAvailablePaymentMethods(): array
    {
        return [
            [
                'code' => 'QRIS',
                'name' => 'QRIS',
                'type' => 'qr',
                'fee' => 0,
                'icon' => '/images/payment/qris.png',
            ],
            [
                'code' => 'DANA',
                'name' => 'DANA',
                'type' => 'ewallet',
                'fee' => 0,
                'icon' => '/images/payment/dana.png',
            ],
            [
                'code' => 'GOPAY',
                'name' => 'GoPay',
                'type' => 'ewallet',
                'fee' => 0,
                'icon' => '/images/payment/gopay.png',
            ],
            [
                'code' => 'OVO',
                'name' => 'OVO',
                'type' => 'ewallet',
                'fee' => 0,
                'icon' => '/images/payment/ovo.png',
            ],
            [
                'code' => 'ALFAMART',
                'name' => 'Alfamart',
                'type' => 'retail',
                'fee' => 2500,
                'icon' => '/images/payment/alfamart.png',
            ],
            [
                'code' => 'INDOMARET',
                'name' => 'Indomaret',
                'type' => 'retail',
                'fee' => 2500,
                'icon' => '/images/payment/indomaret.png',
            ],
        ];
    }

    /**
     * Generate Tokopay signature.
     *
     * @param string $reffId
     * @param float $amount
     * @return string
     */
    protected function generateTokopaySignature(string $reffId, float $amount): string
    {
        $string = $this->tokopayMerchantId . ':' . $reffId . ':' . $amount;
        return hash_hmac('sha256', $string, $this->tokopaySecret);
    }
}