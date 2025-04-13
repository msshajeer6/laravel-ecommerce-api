<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class ProductListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_products()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $token = $user->createToken('test')->plainTextToken;

        Product::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }
}
