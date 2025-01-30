<?php

namespace Tests\Unit\Services;

use App\Services\ShortUrlGenerator;
use Tests\TestCase;

class ShortUrlGeneratorTest extends TestCase
{
    public function test_generates_url_with_configured_domain(): void
    {
        config(['urls.short_url' => 'https://short.test']);
        
        $url = ShortUrlGenerator::generate('abc123');
        
        $this->assertEquals('https://short.test/abc123', $url);
    }

    public function test_generates_url_with_different_domain(): void
    {
        config(['urls.short_url' => 'https://different.com']);
        
        $url = ShortUrlGenerator::generate('xyz789');
        
        $this->assertEquals('https://different.com/xyz789', $url);
    }
}
