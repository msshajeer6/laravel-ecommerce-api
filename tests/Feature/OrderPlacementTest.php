<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class OrderPlacementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_place_order()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $token = $user->createToken('api')->plainTextToken;

        $product = Product::factory()->create(['stock_quantity' => 5]);

        $payload = [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ]
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/orders', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'total', 'items']
            ]);
    }
}
