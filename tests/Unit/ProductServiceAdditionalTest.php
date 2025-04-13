<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class ProductServiceAdditionalTest extends TestCase
{
    use RefreshDatabase;

    protected $productService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productService = new ProductService(new \App\Repositories\ProductRepository);
    }

    public function test_update_product_syncs_categories()
    {
        Storage::fake('public');

        // Create categories (order of creation will define their ids)
        $cat1 = Category::create(['name' => 'Category One', 'slug' => 'category-one']);
        $cat2 = Category::create(['name' => 'Category Two', 'slug' => 'category-two']);
        $cat3 = Category::create(['name' => 'Category Three', 'slug' => 'category-three']);

        $data = [
            'name' => 'Product A',
            'price' => 75,
            'stock_quantity' => 20,
            'categories' => [$cat1->id, $cat2->id],
        ];

        $product = $this->productService->create($data);
        $this->assertCount(2, $product->categories);

        // Now update the product – change categories from cat1 & cat2 to cat2 & cat3
        $updateData = [
            'name' => 'Product A Updated',
            'categories' => [$cat2->id, $cat3->id],
        ];

        $updatedProduct = $this->productService->update($product, $updateData);

        $this->assertEquals('Product A Updated', $updatedProduct->name);
        $this->assertCount(2, $updatedProduct->categories);
        $this->assertEqualsCanonicalizing(
            [$cat2->id, $cat3->id],
            $updatedProduct->categories->pluck('id')->toArray()
        );
    }
}
