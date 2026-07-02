<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $r)
    {
        $orders = Order::with('items')
            ->where('user_id', $r->user()->id)
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_name' => 'required|string|max:120',
            'shipping_phone' => 'required|string|max:20',
            'shipping_city' => 'required|string|max:100',
            'shipping_region' => 'nullable|string|max:100',
            'shipping_address' => 'required|string|max:255',
            'payment_method' => 'required|string|max:50',
        ]);

        $subtotal = 0;
        $orderItems = [];

        foreach ($d['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $price = $product->discount_price ?? $product->price;
            $lineTotal = $price * $item['quantity'];
            $subtotal += $lineTotal;

            $orderItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_price' => $price,
                'quantity' => $item['quantity'],
                'line_total' => $lineTotal,
            ];
        }

        $tax = round($subtotal * 0.18, 2);
        $total = $subtotal + $tax;

        $order = Order::create([
            'user_id' => $r->user()->id,
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping_fee' => 0,
            'discount' => 0,
            'total' => $total,
            'payment_method' => $d['payment_method'],
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'shipping_name' => $d['shipping_name'],
            'shipping_phone' => $d['shipping_phone'],
            'shipping_city' => $d['shipping_city'],
            'shipping_region' => $d['shipping_region'] ?? null,
            'shipping_address' => $d['shipping_address'],
        ]);

        foreach ($orderItems as $oi) {
            $order->items()->create($oi);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Order placed successfully',
            'order' => $order->load('items'),
        ], 201);
    }

    public function show(Order $order)
    {
        if ($order->user_id !== request()->user()->id && request()->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return response()->json($order->load('items'));
    }
}
