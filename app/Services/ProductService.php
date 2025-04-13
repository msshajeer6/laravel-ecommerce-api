<?php
namespace App\Services;
use App\Repositories\ProductRepository;
use App\Services\BaseService;
use App\Models\Product;
use Illuminate\Support\Str;
class ProductService extends BaseService
{
    protected $repo;

    public function __construct(ProductRepository $repo)
    {
        $this->repo = $repo;
    }

    public function create(array $data): Product
    {
        $data['slug'] = Str::slug($data['name']);

        if (isset($data['images'])) {
            $data['images'] = $this->uploadImages($data['images'], 'products');
        }

        $product = $this->repo->create($data);

        if (isset($data['categories'])) {
            $product->categories()->attach($data['categories']);
        }

        return $product->load('categories');
    }

    public function update(Product $product, array $data): Product
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (isset($data['images'])) {
            $data['images'] = $this->uploadImages($data['images'], 'products');
        }

        $this->repo->update($product, $data);

        if (isset($data['categories'])) {
            $product->categories()->sync($data['categories']);
        }

        return $product->load('categories');
    }

    public function delete(Product $product): bool
    {
        return $this->repo->delete($product);
    }

    public function getPaginatedProducts(?string $categorySlug = null, int $perPage = 10)
    {
        return $this->repo->paginateWithCategory($categorySlug, $perPage);
    }

    public function getById($id)
    {
        return $this->repo->findById($id);
    }
}
