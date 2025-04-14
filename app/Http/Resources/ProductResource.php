<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock_quantity,
            'images' => collect($this->images)->map(function ($path) {
                return asset('storage/' . $path);
            }),
            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->pluck('name');
            }),
        ];
    }
}
