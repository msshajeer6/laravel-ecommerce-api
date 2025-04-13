<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $categoryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryService = new CategoryService();
    }

    public function test_create_category_creates_slug()
    {
        $data = [
            'name' => 'New Category'
        ];

        $category = $this->categoryService->create($data);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('new-category', $category->slug);
        $this->assertEquals('New Category', $category->name);
    }

    public function test_update_category_updates_slug()
    {
        $category = Category::create([
            'name' => 'Old Name',
            'slug' => 'old-name',
        ]);

        $updateData = [
            'name' => 'Updated Name'
        ];

        $updatedCategory = $this->categoryService->update($category, $updateData);

        $this->assertEquals('Updated Name', $updatedCategory->name);
        $this->assertEquals('updated-name', $updatedCategory->slug);
    }
}
