<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductVariantController extends Controller
{
    public function edit(Product $product, ProductVariant $variant): View
    {
        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        return view('admin.products.variants.edit', compact('product', 'variant'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'color' => ['required', 'string', 'max:255'],
            'size' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:product_variants,sku'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
        ]);

        $validated['product_id'] = $product->id;

        ProductVariant::create($validated);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Variant created successfully.');
    }

    public function update(Request $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        $validated = $request->validate([
            'color' => ['required', 'string', 'max:255'],
            'size' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:product_variants,sku,' . $variant->id],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
        ]);

        $variant->update($validated);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Variant updated successfully.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        $variant->delete();

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Variant deleted successfully.');
    }
}
