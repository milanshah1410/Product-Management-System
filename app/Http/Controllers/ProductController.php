<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\SearchProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {
        $this->middleware('auth');
    }

    /**
     * Display a listing of products.
     */
    public function index(SearchProductRequest $request): View
    {
        $validated = $request->validated();
        $perPage = $validated['per_page'] ?? 15;
        
        if (!empty($validated['keyword'])) {
            $products = $this->productService->searchProducts(
                $validated['keyword'],
                $validated,
                $perPage
            );
        } else {
            $products = $this->productService->getPaginatedProducts($validated, $perPage);
        }

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        return view('products.create');
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            $product = $this->productService->createProduct($request->validated());
            
            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create product. Please try again.');
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        if (!$this->productService->canUserModifyProduct($product)) {
            abort(403, 'Unauthorized action.');
        }

        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        try {
            $this->productService->updateProduct($product, $request->validated());
            
            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update product. Please try again.');
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if (!$this->productService->canUserModifyProduct($product)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $this->productService->deleteProduct($product);
            
            return redirect()
                ->route('products.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete product. Please try again.');
        }
    }
}