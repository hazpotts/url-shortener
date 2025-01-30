<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\ShortUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_encode_url(): void
    {
        $response = $this->postJson('/api/encode', [
            'url' => 'https://example.com/test'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'short_url',
            ]);

        $this->assertDatabaseHas('short_urls', [
            'original_url' => 'https://example.com/test'
        ]);
    }

    public function test_encode_validates_url(): void
    {
        $response = $this->postJson('/api/encode', [
            'url' => 'not-a-url'
        ]);

        $response->assertStatus(422);
    }

    public function test_can_decode_url(): void
    {
        $shortUrl = ShortUrl::factory()->create();

        $response = $this->postJson('/api/decode', [
            'short_url' => config('urls.short_url_prefix') . '/' . $shortUrl->short_code
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'original_url',
            ])
            ->assertJson([
                'original_url' => $shortUrl->original_url
            ]);
    }

    public function test_decode_returns_404_for_nonexistent_code(): void
    {
        $response = $this->postJson('/api/decode', [
            'short_url' => config('urls.short_url_prefix') . '/nonexistent'
        ]);

        $response->assertStatus(404);
    }
}
