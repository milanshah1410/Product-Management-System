<?php 

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Get all products with pagination and filters.
     */
    public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::with('user:id,name')
            ->active();

        $query = $this->applyFilters($query, $filters);

        return $query->latest()->paginate($perPage);
    }

    /**
     * Find product by ID.
     */
    public function findById(int $id): ?Product
    {
        return Cache::remember("product.{$id}", 3600, function () use ($id) {
            return Product::with('user:id,name')->find($id);
        });
    }

    /**
     * Create a new product.
     */
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            return Product::create($data);
        });
    }

    /**
     * Update an existing product.
     */
    public function update(Product $product, array $data): bool
    {
        return DB::transaction(function () use ($product, $data) {
            $updated = $product->update($data);
            
            if ($updated) {
                Cache::forget("product.{$product->id}");
            }
            
            return $updated;
        });
    }

    /**
     * Delete a product (soft delete).
     */
    public function delete(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            Cache::forget("product.{$product->id}");
            return $product->delete();
        });
    }

    /**
     * Search products with optimized full-text search.
     */
    public function search(string $keyword, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::with('user:id,name')
            ->active()
            ->search($keyword);

        $query = $this->applyFilters($query, $filters);

        return $query->latest()->paginate($perPage);
    }

    /**
     * Apply filters to query.
     */
    private function applyFilters($query, array $filters)
    {
        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $query->priceRange(
                $filters['min_price'] ?? null,
                $filters['max_price'] ?? null
            );
        }

        if (isset($filters['start_date']) || isset($filters['end_date'])) {
            $query->dateRange(
                $filters['start_date'] ?? null,
                $filters['end_date'] ?? null
            );
        }

        return $query;
    }
}