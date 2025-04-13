<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an order is created successfully,
     * order items are recorded, totals computed, and stock updated.
     */
    public function test_it_creates_order_successfully()
    {
        // Create a dummy user (ensure you have a factory for User or create manually)
        $user = User::factory()->create();

        // Create two sample products.
        $product1 = Product::create([
            'name' => 'Test Product 1',
            'slug' => 'test-product-1',
            'description' => 'Description for product 1',
            'price' => 50,
            'stock_quantity' => 20,
            'images' => [],
        ]);

        $product2 = Product::create([
            'name' => 'Test Product 2',
            'slug' => 'test-product-2',
            'description' => 'Description for product 2',
            'price' => 30,
            'stock_quantity' => 10,
            'images' => [],
        ]);

        // Define order items
        $items = [
            ['product_id' => $product1->id, 'quantity' => 2], // Expected cost 2 x 50 = 100
            ['product_id' => $product2->id, 'quantity' => 3], // Expected cost 3 x 30 = 90
        ];

        $orderService = new OrderService();
        $order = $orderService->createOrder($items, $user->id);

        // Assert an Order was created
        $this->assertInstanceOf(Order::class, $order);

        // Check that order has 2 items
        $this->assertCount(2, $order->items);

        // Check that the total is calculated correctly (100 + 90 = 190)
        $this->assertEquals(190, $order->total);

        // Check that the stock quantities have been updated correctly
        $this->assertEquals(18, $product1->fresh()->stock_quantity);
        $this->assertEquals(7, $product2->fresh()->stock_quantity);
    }

    /**
     * Test that the service throws an exception when stock is insufficient.
     */
    public function test_it_fails_for_insufficient_stock()
    {
        // Expect an exception to be thrown
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Insufficient stock");

        // Create a dummy user
        $user = User::factory()->create();

        // Create a product with very low stock
        $product = Product::create([
            'name' => 'Product 1',
            'slug' => 'product-1',
            'description' => 'Description for product 1',
            'price' => 50,
            'stock_quantity' => 1,
            'images' => [],
        ]);

        // Attempt to create an order for more than available stock
        $items = [
            ['product_id' => $product->id, 'quantity' => 2]
        ];

        $orderService = new OrderService();
        $orderService->createOrder($items, $user->id);
    }
}
