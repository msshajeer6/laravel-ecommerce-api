<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function paginateWithCategory($categorySlug = null, $perPage = 10)
    {
        $query = Product::query()->with('categories');

        if ($categorySlug) {
            $query->whereHas('categories', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        return $query->paginate($perPage);
    }

    public function findById($id)
    {
        return Product::with('categories')->findOrFail($id);
    }

    public function create($data)
    {
        return Product::create($data);
    }

    public function update($product, $data)
    {
        $product->update($data);
        return $product;
    }

    public function delete($product)
    {
        return $product->delete();
    }
}
