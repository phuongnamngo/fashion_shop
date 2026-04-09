<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Support\ProductMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['required', 'image', 'max:2048'],
        ]);

        $nextOrder = (int) ProductImage::query()
            ->where('product_id', $product->id)
            ->max('sort_order');

        foreach ($request->file('images', []) as $file) {
            $nextOrder++;
            $path = $file->store("products/gallery/{$product->id}", 'public');
            ProductImage::query()->create([
                'product_id' => $product->id,
                'image_url' => $path,
                'sort_order' => $nextOrder,
            ]);
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Gallery images uploaded successfully.');
    }

    public function destroy(Product $product, ProductImage $image): RedirectResponse
    {
        if ($image->product_id !== $product->id) {
            abort(404);
        }

        ProductMedia::deletePublicFileIfManaged($image->image_url);
        $image->delete();

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Gallery image removed successfully.');
    }
}
