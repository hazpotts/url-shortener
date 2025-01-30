<?php

namespace Tests\Unit\Services;

use App\Models\ShortUrl;
use App\Services\UrlService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlServiceTest extends TestCase
{
    use RefreshDatabase;

    private UrlService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(UrlService::class);
    }

    public function test_encode_creates_url_with_valid_code(): void
    {
        $url = 'https://example.com/test';
        
        $shortUrl = $this->service->encode($url);
        
        $this->assertDatabaseHas('short_urls', [
            'id' => $shortUrl->id,
            'original_url' => $url,
        ]);
        
        // Verify code format matches configuration
        $config = config('urls.code_generation');
        $this->assertEquals($config['length'], strlen($shortUrl->short_code));
        $this->assertMatchesRegularExpression(
            '/^[' . preg_quote($config['allowed_chars'], '/') . ']+$/',
            $shortUrl->short_code
        );
    }

    public function test_encode_generates_unique_codes(): void
    {
        $codes = collect();
        
        // Generate multiple URLs and collect their codes
        for ($i = 0; $i < 50; $i++) {
            $shortUrl = $this->service->encode("https://example.com/test{$i}");
            $codes->push($shortUrl->short_code);
        }
        
        // Verify all codes are unique
        $this->assertEquals($codes->count(), $codes->unique()->count());
    }

    public function test_decode_returns_url_for_valid_code(): void
    {
        $shortUrl = ShortUrl::factory()->create();
        $prefix = config('urls.short_url_prefix');
        
        $result = $this->service->decode($prefix . '/' . $shortUrl->short_code);
        
        $this->assertNotNull($result);
        $this->assertEquals($shortUrl->id, $result->id);
        $this->assertEquals($shortUrl->original_url, $result->original_url);
    }

    public function test_decode_handles_url_with_prefix(): void
    {
        $shortUrl = ShortUrl::factory()->create();
        $prefix = config('urls.short_url_prefix');
        
        $result = $this->service->decode($prefix . '/' . $shortUrl->short_code);
        
        $this->assertNotNull($result);
        $this->assertEquals($shortUrl->id, $result->id);
        $this->assertEquals($shortUrl->original_url, $result->original_url);
    }

    public function test_decode_returns_null_for_nonexistent_code(): void
    {
        $result = $this->service->decode('nonexistent');
        
        $this->assertNull($result);
    }
}
