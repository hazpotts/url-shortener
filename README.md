# URL Shortener API

A simple URL shortener service built with Laravel that provides endpoints to encode and decode URLs.

## API Endpoints

### Encode URL

Convert a long URL into a shortened version.

```http
POST /api/encode

{
    "url": "https://example.com/very/long/url"
}
```

#### Response

```json
{
    "short_url": "https://short.est/abc123"
}
```

### Decode URL

Retrieve the original URL from a shortened URL.

```http
POST /api/decode

{
    "short_url": "https://short.est/abc123"
}
```

#### Response

```json
{
    "original_url": "https://example.com/very/long/url"
}
```

## Error Responses

### Validation Errors (422 Unprocessable Entity)

Both endpoints will return a 422 status code for validation errors:

```json
{
    "error": "Validation failed",
    "errors": {
        "url": [
            "The url field is required",
            "The url field must be a valid URL"
        ]
    }
}
```

### Not Found Error (404 Not Found)

The decode endpoint will return a 404 status for non-existent short URLs:

```json
{
    "error": "URL not found"
}
```

### Server Error (500 Internal Server Error)

For unexpected errors, the endpoints will return a 500 status:

```json
{
    "error": "Failed to create short URL"
}
```