<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    /**
     * Get paginated products with filters.
     */
    public function getPaginatedProducts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        try {
            return $this->productRepository->getAllPaginated($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching products: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get a single product by ID.
     */
    public function getProductById(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }

    /**
     * Create a new product.
     */
    public function createProduct(array $validatedData): Product
    {
        try {
            $validatedData['user_id'] = Auth::id();
            
            // Sanitize description HTML to prevent XSS
            $validatedData['description'] = $this->sanitizeHtml($validatedData['description']);
            
            $product = $this->productRepository->create($validatedData);
            
            Log::info('Product created', ['product_id' => $product->id, 'user_id' => Auth::id()]);
            
            return $product;
        } catch (\Exception $e) {
            Log::error('Error creating product: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing product.
     */
    public function updateProduct(Product $product, array $validatedData): bool
    {
        try {
            // Sanitize description HTML
            if (isset($validatedData['description'])) {
                $validatedData['description'] = $this->sanitizeHtml($validatedData['description']);
            }
            
            $updated = $this->productRepository->update($product, $validatedData);
            
            if ($updated) {
                Log::info('Product updated', ['product_id' => $product->id, 'user_id' => Auth::id()]);
            }
            
            return $updated;
        } catch (\Exception $e) {
            Log::error('Error updating product: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a product.
     */
    public function deleteProduct(Product $product): bool
    {
        try {
            $deleted = $this->productRepository->delete($product);
            
            if ($deleted) {
                Log::info('Product deleted', ['product_id' => $product->id, 'user_id' => Auth::id()]);
            }
            
            return $deleted;
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Search products with keyword.
     */
    public function searchProducts(string $keyword, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        try {
            return $this->productRepository->search($keyword, $filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error searching products: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sanitize HTML content to prevent XSS attacks.
     */
    private function sanitizeHtml(string $html): string
    {
        // Allow only safe HTML tags
        $allowedTags = '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><blockquote><code><pre>';
        
        return strip_tags($html, $allowedTags);
    }

    /**
     * Check if user can modify the product.
     */
    public function canUserModifyProduct(Product $product): bool
    {
        $user = Auth::user();
        
        // Admins can modify any product
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        // Users can only modify their own products
        return $product->user_id === $user->id;
    }
}