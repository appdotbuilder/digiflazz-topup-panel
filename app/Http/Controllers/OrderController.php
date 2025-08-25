<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\DigifazzService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Digiflazz service instance.
     *
     * @var DigifazzService
     */
    protected $digifazzService;

    /**
     * Payment service instance.
     *
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * Create a new controller instance.
     *
     * @param DigifazzService $digifazzService
     * @param PaymentService $paymentService
     */
    public function __construct(DigifazzService $digifazzService, PaymentService $paymentService)
    {
        $this->digifazzService = $digifazzService;
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['product.category', 'user'])
            ->when(auth()->check() && !auth()->user()->isAdmin(), function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->latest()
            ->paginate(15);

        return Inertia::render('orders/index', [
            'orders' => $orders
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $product = Product::findOrFail($request->product_id);
        
        // Calculate total amount
        $quantity = $request->quantity ?? 1;
        $unitPrice = $product->current_price;
        $totalAmount = $unitPrice * $quantity;

        // Check user balance if authenticated
        if (auth()->check()) {
            $user = auth()->user();
            if (!$user->hasSufficientBalance($totalAmount)) {
                return back()->withErrors(['balance' => 'Insufficient balance. Please top up your account.']);
            }
        }

        // Create order
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'customer_email' => $request->customer_email ?? auth()->user()?->email,
            'customer_whatsapp' => $request->customer_whatsapp ?? auth()->user()?->whatsapp,
            'game_id' => $request->game_id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        // If user is authenticated, deduct balance and process immediately
        if (auth()->check() && auth()->user()->hasSufficientBalance($totalAmount)) {
            auth()->user()->deductBalance($totalAmount);
            $order->update(['payment_status' => 'paid']);
            
            // Process with Digiflazz
            $this->processDigiflazzOrder($order);
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully! Processing your request...');
        }

        // For guest users or insufficient balance, redirect to payment
        return $this->paymentService->createPayment($order);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        // Check if user can view this order
        if (!auth()->check() || (!auth()->user()->isAdmin() && $order->user_id !== auth()->id())) {
            abort(403);
        }

        $order->load(['product.category', 'user']);

        return Inertia::render('orders/show', [
            'order' => $order
        ]);
    }

    /**
     * Process order with Digiflazz.
     *
     * @param Order $order
     * @return void
     */
    protected function processDigiflazzOrder(Order $order)
    {
        try {
            $order->update(['status' => 'processing']);
            
            $result = $this->digifazzService->processOrder($order);
            
            if ($result['success']) {
                $order->update([
                    'status' => 'completed',
                    'digiflazz_data' => $result['data'],
                ]);
            } else {
                $order->update([
                    'status' => 'failed',
                    'notes' => $result['message'],
                ]);
                
                // Refund balance if order failed
                if ($order->user) {
                    $order->user->addBalance($order->total_amount);
                }
            }
        } catch (\Exception $e) {
            $order->update([
                'status' => 'failed',
                'notes' => 'Processing error: ' . $e->getMessage(),
            ]);
            
            // Refund balance if order failed
            if ($order->user) {
                $order->user->addBalance($order->total_amount);
            }
        }
    }


}