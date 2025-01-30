<?php

namespace App\Services;

use App\Models\ShortUrl;
use Illuminate\Support\Facades\Config;

class UrlService
{
    public function encode(string $url): ShortUrl
    {
        do {
            $code = $this->generateCode();
        } while (ShortUrl::where('short_code', $code)->exists());

        return ShortUrl::create([
            'original_url' => $url,
            'short_code' => $code,
        ]);
    }

    private function generateCode(): string
    {
        $config = Config::get('urls.code_generation');
        $allowedChars = $config['allowed_chars'];
        $codeLength = $config['length'];
        
        $code = '';
        $maxIndex = strlen($allowedChars) - 1;
        
        for ($i = 0; $i < $codeLength; $i++) {
            $code .= $allowedChars[random_int(0, $maxIndex)];
        }
        
        return $code;
    }

    public function decode(string $shortUrl): ?ShortUrl
    {
        // remove prefix and path separator from short URL if it exists
        $prefix = config('urls.short_url_prefix');
        $code = str_starts_with($shortUrl, $prefix) ? substr($shortUrl, strlen($prefix)) : $shortUrl;
        $code = ltrim($code, '/');

        return ShortUrl::where('short_code', $code)->first();
    }
}
