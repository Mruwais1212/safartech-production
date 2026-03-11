<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SafeMediaDownloadService
{
    public function downloadToPublicDisk(string $url, string $directory = 'uploads'): ?string
    {
        if (! $this->isAllowedUrl($url)) {
            return null;
        }

        $timeout = (int) config('media.timeout_seconds', 10);
        $maxSize = (int) config('media.max_download_size', 5 * 1024 * 1024);
        $allowedTypes = config('media.allowed_content_types', []);

        $response = Http::withoutRedirecting()->timeout($timeout)->retry(2, 200)->withHeaders([
            'Accept' => 'image/jpeg,image/png,image/webp',
        ])->get($url);

        if (! $response->successful()) {
            return null;
        }

        $contentLength = (int) ($response->header('Content-Length') ?? 0);
        if ($contentLength > 0 && $contentLength > $maxSize) {
            return null;
        }

        $contentType = strtolower(trim(explode(';', (string) $response->header('Content-Type'))[0] ?? ''));
        if (! in_array($contentType, $allowedTypes, true)) {
            return null;
        }

        $body = $response->body();
        if (strlen($body) === 0 || strlen($body) > $maxSize) {
            return null;
        }

        $extension = match ($contentType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $filename = $directory.'/destination-'.bin2hex(random_bytes(12)).'.'.$extension;
        Storage::disk('public')->put($filename, $body);

        return basename($filename);
    }

    private function isAllowedUrl(string $url): bool
    {
        $parts = parse_url($url);
        if (! is_array($parts)) {
            return false;
        }

        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');

        if ($scheme !== 'https' || $host === '') {
            return false;
        }

        if (! $this->hostAllowed($host)) {
            return false;
        }

        $records = @dns_get_record($host, DNS_A + DNS_AAAA) ?: [];
        foreach ($records as $record) {
            $ip = $record['ip'] ?? $record['ipv6'] ?? null;
            if ($ip && ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return false;
            }
        }

        return true;
    }

    private function hostAllowed(string $host): bool
    {
        foreach ((array) config('media.allowed_hosts', []) as $pattern) {
            $pattern = strtolower($pattern);
            if ($pattern === $host) {
                return true;
            }
            if (str_starts_with($pattern, '*.')) {
                $suffix = substr($pattern, 1);
                if (str_ends_with($host, $suffix)) {
                    return true;
                }
            }
        }

        return false;
    }
}
