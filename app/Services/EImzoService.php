<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EImzoService
{
    protected string $serverUrl;

    public function __construct()
    {
        $this->serverUrl = config('eimzo.server_url', 'http://127.0.0.1:8080');
    }

    public function getChallenge(): array
    {
        try {
            $response = Http::get("{$this->serverUrl}/frontend/challenge");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 0,
                'message' => 'Failed to get challenge from E-IMZO server'
            ];
        } catch (\Exception $e) {
            Log::error('E-IMZO getChallenge error: ' . $e->getMessage());
            return [
                'status' => 0,
                'message' => 'E-IMZO server connection error: ' . $e->getMessage()
            ];
        }
    }

    public function authenticate(string $pkcs7, string $userIp, string $host): array
    {
        try {
            $response = Http::withHeaders([
                'X-Real-IP' => $userIp,
                'Host' => $host,
            ])->withBody($pkcs7, 'text/plain')
              ->post("{$this->serverUrl}/backend/auth");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 0,
                'message' => 'Authentication failed: ' . $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('E-IMZO authenticate error: ' . $e->getMessage());
            return [
                'status' => 0,
                'message' => 'E-IMZO server connection error: ' . $e->getMessage()
            ];
        }
    }

    public function attachTimestamp(string $pkcs7, string $userIp, string $host): array
    {
        try {
            $response = Http::withHeaders([
                'X-Real-IP' => $userIp,
                'Host' => $host,
            ])->withBody($pkcs7, 'text/plain')
              ->post("{$this->serverUrl}/frontend/timestamp/pkcs7");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 0,
                'message' => 'Failed to attach timestamp: ' . $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('E-IMZO attachTimestamp error: ' . $e->getMessage());
            return [
                'status' => 0,
                'message' => 'E-IMZO server connection error: ' . $e->getMessage()
            ];
        }
    }

    public function verifySignature(string $pkcs7, string $userIp, string $host): array
    {
        try {
            $response = Http::withHeaders([
                'X-Real-IP' => $userIp,
                'Host' => $host,
            ])->withBody($pkcs7, 'text/plain')
              ->post("{$this->serverUrl}/backend/pkcs7/verify/attached");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 0,
                'message' => 'Signature verification failed: ' . $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('E-IMZO verifySignature error: ' . $e->getMessage());
            return [
                'status' => 0,
                'message' => 'E-IMZO server connection error: ' . $e->getMessage()
            ];
        }
    }

    public function extractCertificateInfo(array $subjectCertificateInfo): array
    {
        $subjectName = $subjectCertificateInfo['subjectName'] ?? [];

        return [
            'name' => $subjectName['CN'] ?? null,
            'pinfl' => $subjectName['1.2.860.3.16.1.2'] ?? null,
            'inn' => $subjectName['1.2.860.3.16.1.1'] ?? ($subjectName['UID'] ?? null),
            'organization' => $subjectName['O'] ?? null,
            'position' => $subjectName['T'] ?? null,
            'serial_number' => $subjectCertificateInfo['serialNumber'] ?? null,
            'valid_from' => isset($subjectCertificateInfo['validFrom'])
                ? \Carbon\Carbon::parse($subjectCertificateInfo['validFrom'])
                : null,
            'valid_to' => isset($subjectCertificateInfo['validTo'])
                ? \Carbon\Carbon::parse($subjectCertificateInfo['validTo'])
                : null,
        ];
    }

    public function getStatusMessage(int $status): string
    {
        return match ($status) {
            1 => 'Success',
            -1 => 'Failed to verify certificate status',
            -5 => 'Invalid signing time. Check computer date/time',
            -10 => 'Invalid digital signature',
            -11 => 'Invalid certificate',
            -12 => 'Certificate invalid at signing date',
            -20 => 'Challenge not found or expired',
            -21 => 'Invalid timestamp signature or hash',
            -22 => 'Invalid timestamp certificate',
            -23 => 'Timestamp certificate invalid at signing date',
            default => 'Unknown error',
        };
    }
}
