<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductAdminTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_create_product()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/products', [
                'name' => 'Test Product',
                'price' => 99.99,
                'stock_quantity' => 10
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Product']);
    }
}
