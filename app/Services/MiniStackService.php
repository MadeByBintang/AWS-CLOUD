<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MiniStackService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('MINISTACK_URL', 'http://localhost:4566');
    }

    public function createBucket(string $bucketName): bool
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/xml',
            ])->put("{$this->baseUrl}/{$bucketName}");

            Log::info('MiniStack createBucket: ' . $bucketName . ' - status: ' . $response->status());

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('MiniStack createBucket: ' . $e->getMessage());
            return false;
        }
    }

    public function listObjects(string $bucketName): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$bucketName}");

            if ($response->successful()) {
                // Parse XML response dari S3
                $xml   = simplexml_load_string($response->body());
                $files = [];

                if ($xml && isset($xml->Contents)) {
                    foreach ($xml->Contents as $item) {
                        $files[] = [
                            'key'           => (string) $item->Key,
                            'size'          => (int) $item->Size,
                            'last_modified' => (string) $item->LastModified,
                        ];
                    }
                }

                return $files;
            }

            return [];
        } catch (\Exception $e) {
            Log::error('MiniStack listObjects: ' . $e->getMessage());
            return [];
        }
    }

    public function uploadObject(string $bucketName, string $fileName, $fileContents, string $mimeType): bool
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => $mimeType,
            ])->withBody($fileContents, $mimeType)
                ->put("{$this->baseUrl}/{$bucketName}/{$fileName}");

            // Jika LocalStack direstart, bucket mungkin hilang. Recreate bucket lalu coba upload ulang.
            if ($response->status() === 404) {
                Log::warning("MiniStack upload: Bucket {$bucketName} missing (404). Attempting to recreate...");
                $this->createBucket($bucketName);
                
                $response = Http::withHeaders([
                    'Content-Type' => $mimeType,
                ])->withBody($fileContents, $mimeType)
                    ->put("{$this->baseUrl}/{$bucketName}/{$fileName}");
            }

            Log::info("MiniStack upload: {$bucketName}/{$fileName} - status: " . $response->status());

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('MiniStack uploadObject: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteObject(string $bucketName, string $fileName): bool
    {
        try {
            $response = Http::delete("{$this->baseUrl}/{$bucketName}/{$fileName}");
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('MiniStack deleteObject: ' . $e->getMessage());
            return false;
        }
    }

    public function getObjectUrl(string $bucketName, string $fileName): string
    {
        return "{$this->baseUrl}/{$bucketName}/{$fileName}";
    }

    public function deleteBucket(string $bucketName): bool
    {
        try {
            $response = Http::delete("{$this->baseUrl}/{$bucketName}");
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('MiniStack deleteBucket: ' . $e->getMessage());
            return false;
        }
    }

    public function generateCredentials(string $username): array|false
    {
        return [
            'access_key' => 'key-' . $username . '-' . uniqid(),
            'secret_key' => bin2hex(random_bytes(20)),
        ];
    }
}
