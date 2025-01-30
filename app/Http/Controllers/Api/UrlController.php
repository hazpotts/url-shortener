<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DecodeUrlRequest;
use App\Http\Requests\EncodeUrlRequest;
use App\Services\ShortUrlGenerator;
use App\Services\UrlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Redirect;

class UrlController extends Controller
{
    public function __construct(
        private readonly UrlService $urlService
    ) {}

    public function encode(EncodeUrlRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $shortUrl = $this->urlService->encode($validatedData['url']);

            return response()->json([
                'short_url' => config('urls.short_url_prefix') . '/' . $shortUrl->short_code,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create short URL',
            ], 500);
        }
    }

    public function decode(DecodeUrlRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $shortUrl = $this->urlService->decode($validatedData['short_url']);

            if (!$shortUrl) {
                return response()->json([
                    'error' => 'URL not found',
                ], 404);
            }

            return response()->json([
                'original_url' => $shortUrl->original_url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to decode URL',
            ], 500);
        }
    }
}
