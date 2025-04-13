<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Services\CategoryService;
use App\Http\Resources\CategoryResource;
class CategoryController extends Controller
{
    protected $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    public function index(Request $request)
    {
        $categories = Category::query()->paginate(10);
        return CategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:categories',
        ]);
        $category = $this->categoryService->create($data);
        return new CategoryResource($category);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return new CategoryResource($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|unique:categories,name,' . $id,
        ]);
        $this->categoryService->update($category, $data);
        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        if ($category->products()->whereHas('orderItems')->exists()) {
            return response()->json(['message' => 'Cannot delete category with products used in orders.'], 400);
        }
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
