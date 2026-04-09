<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminProductMediaTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): Admin
    {
        return Admin::query()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.test',
            'password' => 'secret123',
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    private function makeCategory(): Category
    {
        return Category::query()->create([
            'name' => 'Shoes',
            'slug' => 'shoes',
        ]);
    }

    #[Test]
    public function store_product_saves_thumbnail_under_public_disk(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        $category = $this->makeCategory();
        $thumb = UploadedFile::fake()->image('t.jpg', 80, 80);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Runner',
            'slug' => 'runner',
            'description' => 'Fast',
            'base_price' => '99.00',
            'status' => '1',
            'thumbnail' => $thumb,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $product = Product::query()->first();
        $this->assertNotNull($product);
        $this->assertStringStartsWith('products/thumbnails/', $product->thumbnail);
        Storage::disk('public')->assertExists($product->thumbnail);
    }

    #[Test]
    public function update_without_new_thumbnail_keeps_existing_path(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        $category = $this->makeCategory();
        $path = 'products/thumbnails/keep.jpg';
        Storage::disk('public')->put($path, 'x');

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'P',
            'slug' => 'p',
            'description' => 'd',
            'base_price' => 10,
            'thumbnail' => $path,
            'status' => true,
        ]);

        $this->actingAs($admin, 'admin')->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'name' => 'P2',
            'slug' => 'p',
            'description' => 'd2',
            'base_price' => '11.00',
            'status' => '1',
        ])->assertRedirect(route('admin.products.edit', $product));

        $product->refresh();
        $this->assertSame($path, $product->thumbnail);
    }

    #[Test]
    public function update_with_new_thumbnail_deletes_old_managed_file(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        $category = $this->makeCategory();
        $old = 'products/thumbnails/old.jpg';
        Storage::disk('public')->put($old, 'old-bytes');

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'P',
            'slug' => 'p',
            'description' => 'd',
            'base_price' => 10,
            'thumbnail' => $old,
            'status' => true,
        ]);

        $newFile = UploadedFile::fake()->image('n.jpg');

        $this->actingAs($admin, 'admin')->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'name' => 'P',
            'slug' => 'p',
            'description' => 'd',
            'base_price' => '10.00',
            'status' => '1',
            'thumbnail' => $newFile,
        ])->assertRedirect(route('admin.products.edit', $product));

        Storage::disk('public')->assertMissing($old);
        $product->refresh();
        $this->assertNotSame($old, $product->thumbnail);
        Storage::disk('public')->assertExists($product->thumbnail);
    }

    #[Test]
    public function gallery_upload_creates_ordered_records(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        $category = $this->makeCategory();
        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'P',
            'slug' => 'p',
            'description' => 'd',
            'base_price' => 10,
            'thumbnail' => 'products/thumbnails/t.jpg',
            'status' => true,
        ]);
        Storage::disk('public')->put($product->thumbnail, 'x');

        $a = UploadedFile::fake()->image('a.jpg');
        $b = UploadedFile::fake()->image('b.jpg');

        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.images.store', $product), [
                'images' => [$a, $b],
            ])
            ->assertRedirect(route('admin.products.edit', $product));

        $rows = ProductImage::query()->where('product_id', $product->id)->orderBy('sort_order')->get();
        $this->assertCount(2, $rows);
        $this->assertSame(1, $rows[0]->sort_order);
        $this->assertSame(2, $rows[1]->sort_order);
        Storage::disk('public')->assertExists($rows[0]->image_url);
        Storage::disk('public')->assertExists($rows[1]->image_url);
    }

    #[Test]
    public function cannot_delete_gallery_image_from_wrong_product(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        $category = $this->makeCategory();

        $p1 = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'A',
            'slug' => 'a',
            'description' => 'd',
            'base_price' => 1,
            'thumbnail' => 'products/thumbnails/a.jpg',
            'status' => true,
        ]);
        $p2 = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'B',
            'slug' => 'b',
            'description' => 'd',
            'base_price' => 2,
            'thumbnail' => 'products/thumbnails/b.jpg',
            'status' => true,
        ]);

        $imgPath = 'products/gallery/'.$p1->id.'/g.jpg';
        Storage::disk('public')->put($imgPath, 'g');
        $image = ProductImage::query()->create([
            'product_id' => $p1->id,
            'image_url' => $imgPath,
            'sort_order' => 1,
        ]);

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.products.images.destroy', [$p2, $image]))
            ->assertNotFound();

        $this->assertTrue(ProductImage::query()->whereKey($image->id)->exists());
    }

    #[Test]
    public function destroy_product_removes_managed_files(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        $category = $this->makeCategory();

        $thumb = 'products/thumbnails/t.jpg';
        Storage::disk('public')->put($thumb, 't');

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'P',
            'slug' => 'p',
            'description' => 'd',
            'base_price' => 10,
            'thumbnail' => $thumb,
            'status' => true,
        ]);

        $gPath = 'products/gallery/'.$product->id.'/g.jpg';
        Storage::disk('public')->put($gPath, 'g');
        ProductImage::query()->create([
            'product_id' => $product->id,
            'image_url' => $gPath,
            'sort_order' => 1,
        ]);

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        Storage::disk('public')->assertMissing($thumb);
        Storage::disk('public')->assertMissing($gPath);
        $this->assertNull(Product::query()->find($product->id));
    }
}
