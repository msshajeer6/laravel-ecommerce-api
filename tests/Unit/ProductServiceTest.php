<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Services\ProductService;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public'); // Move to setup for all tests
        $this->service = new ProductService(new ProductRepository);
    }

    public function test_create_product_with_images()
    {
        $data = [
            'name' => 'Test Unit Product',
            'price' => 99.99,
            'stock_quantity' => 10,
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
                UploadedFile::fake()->image('image2.jpg')
            ],
            'categories' => []
        ];

        $product = $this->service->create($data);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Unit Product', $product->name);
        $this->assertCount(2, $product->images);

        foreach ($product->images as $path) {
            Storage::disk('public')->assertExists($path);
        }
    }

    public function test_update_product_with_new_image()
    {
        $product = Product::create([
            'name' => 'Original Product',
            'slug' => 'original-product',
            'price' => 50,
            'stock_quantity' => 20,
            'images' => [],
        ]);

        $update = [
            'name' => 'Updated Product',
            'price' => 75,
            'images' => [UploadedFile::fake()->image('updated1.jpg')],
            'categories' => []
        ];

        $updated = $this->service->update($product, $update);

        $this->assertEquals('Updated Product', $updated->name);
        $this->assertEquals(75, $updated->price);
        $this->assertCount(1, $updated->images);

        Storage::disk('public')->assertExists($updated->images[0]);
    }
}
