<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $items, int $userId): Order
    {
        return DB::transaction(function () use ($items, $userId) {
            $total = 0;
            $validatedItems = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $validatedItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];
            }

            $order = Order::create([
                'user_id' => $userId,
                'total' => 0,
            ]);

            foreach ($validatedItems as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price']
                ]);

                $item['product']->decrement('stock_quantity', $item['quantity']);
                $total += $item['price'] * $item['quantity'];
            }

            $order->update(['total' => $total]);

            return $order;
        });
    }
}
