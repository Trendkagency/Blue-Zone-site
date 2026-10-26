<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageFallbackRouteTest extends TestCase
{
    use RefreshDatabase;
    public function test_storage_fallback_serves_public_file_without_403(): void
    {
        Storage::disk('public')->put('test-image.jpg', 'fake-image-binary-data');

        $response = $this->get('/storage/test-image.jpg');

        $response->assertStatus(200);
        $this->assertEquals('fake-image-binary-data', file_get_contents($response->getFile()->getPathname()));

        Storage::disk('public')->delete('test-image.jpg');
    }

    public function test_public_storage_fallback_serves_public_file_without_403(): void
    {
        Storage::disk('public')->put('test-image-public.jpg', 'fake-image-binary-data-2');

        $response = $this->get('/public/storage/test-image-public.jpg');

        $response->assertStatus(200);
        $this->assertEquals('fake-image-binary-data-2', file_get_contents($response->getFile()->getPathname()));

        Storage::disk('public')->delete('test-image-public.jpg');
    }

    public function test_storage_fallback_returns_404_for_non_existent_file(): void
    {
        $response = $this->get('/storage/completely-non-existent-image-xyz.jpg');

        $response->assertStatus(404);
    }
}
