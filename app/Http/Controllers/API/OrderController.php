<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\OrderService;
use App\Http\Resources\OrderResource;



class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // List authenticated user's orders
    public function index(Request $request)
    {
        $query = Order::query();
        $orders = $query->where('user_id', $request->user()->id)->with('items.product')->paginate(10);
        return OrderResource::collection($orders);
    }

    // Create a new order (customer)
    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $order = $this->orderService->createOrder($data['items'], $request->user()->id);
            return new OrderResource($order->load('items.product'));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }


    // Single order details
    public function show(Order $order)
    {
        return new OrderResource($order->where('user_id', auth()->id())
            ->with('items.product')->firstOrFail());
    }
}
