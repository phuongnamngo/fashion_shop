<?php

namespace Tests\Unit;

use App\Support\ProductMedia;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductMediaTest extends TestCase
{
    #[Test]
    public function public_url_keeps_absolute_http_urls(): void
    {
        $url = 'https://example.com/a.jpg';
        $this->assertSame($url, ProductMedia::publicUrl($url));
    }

    #[Test]
    public function public_url_maps_managed_paths_to_storage(): void
    {
        $this->assertStringContainsString(
            'storage/products/thumbnails/x.jpg',
            ProductMedia::publicUrl('products/thumbnails/x.jpg')
        );
    }

    #[Test]
    public function is_managed_detects_prefixes_only(): void
    {
        $this->assertTrue(ProductMedia::isManagedRelativePath('products/thumbnails/a.png'));
        $this->assertTrue(ProductMedia::isManagedRelativePath('products/gallery/1/a.png'));
        $this->assertFalse(ProductMedia::isManagedRelativePath('https://x.com/a.png'));
        $this->assertFalse(ProductMedia::isManagedRelativePath('evil/../products/thumbnails/x'));
    }
}
