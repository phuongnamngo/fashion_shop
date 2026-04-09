<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\ProductMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['category', 'variants'])
            ->latest()
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['required', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'thumbnail' => ['required', 'image', 'max:2048'],
            'status' => ['nullable', 'boolean'],
        ]);

        $validated['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
        $validated['status'] = $request->boolean('status');

        Product::query()->create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        $product->load(['variants', 'images']);

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug,'.$product->id],
            'description' => ['required', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'status' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('thumbnail')) {
            ProductMedia::deletePublicFileIfManaged($product->thumbnail);
            $validated['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
        } else {
            unset($validated['thumbnail']);
        }

        $validated['status'] = $request->boolean('status');

        $product->update($validated);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->load('images');

        foreach ($product->images as $image) {
            ProductMedia::deletePublicFileIfManaged($image->image_url);
        }

        ProductMedia::deletePublicFileIfManaged($product->thumbnail);

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
