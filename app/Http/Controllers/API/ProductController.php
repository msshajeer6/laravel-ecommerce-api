<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    protected $productService;
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $categorySlug = $request->get('category', 'all');
        $cacheKey = "products_page_{$page}_category_{$categorySlug}";

        $category = $categorySlug === 'all' ? null : $categorySlug;

        $products = Cache::remember($cacheKey, 60, function () use ($category) {
            return $this->productService->getPaginatedProducts($category);
        });

        // Return resource collection with pagination meta
        return ProductResource::collection($products);
    }

    public function show(Product $product)
    {
        return new ProductResource($product->load('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $product = $this->productService->create($data);
        Cache::flush();

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|unique:products,name,' . $id,
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'stock_quantity' => 'sometimes|required|integer',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $product = $this->productService->update($product, $data);
        Cache::flush();

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        if ($product->orderItems()->exists()) {
            return response()->json(['message' => 'Cannot delete product with existing orders.'], 400);
        }
        $this->productService->delete($product);
        Cache::flush();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
