<?php

namespace App\Repositories\Interfaces;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    
    public function findById(int $id): ?Product;
    
    public function create(array $data): Product;
    
    public function update(Product $product, array $data): bool;
    
    public function delete(Product $product): bool;
    
    public function search(string $keyword, array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
