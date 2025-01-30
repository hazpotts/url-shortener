<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class ShortUrlGenerator
{
    public static function generate(string $code): string
    {
        $url = Config::get('urls.short_url');
        return "{$url}/{$code}";
    }
}
