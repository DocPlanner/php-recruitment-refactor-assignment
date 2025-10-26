<?php

declare(strict_types=1);

namespace App\Infrastructure\ApiClient;

use App\Exception\ApiConnectionException;
use App\Exception\InvalidApiResponseException;
use JsonException;

class HttpDoctorApiClient implements DoctorApiClientInterface
{
    public function __construct(
        private string $baseUrl,
        private string $username,
        private string $password
    ) {
    }

    public function getDoctors(): array
    {
        $endpoint = $this->baseUrl . '/api/doctors';
        $response = $this->fetchData($endpoint);
        
        return $this->parseJsonResponse($response);
    }

    public function getDoctorSlots(int $doctorId): array
    {
        $endpoint = $this->baseUrl . '/api/doctors/' . $doctorId . '/slots';
        $response = $this->fetchData($endpoint);
        
        return $this->parseJsonResponse($response);
    }

    private function fetchData(string $url): string
    {
        $auth = base64_encode(sprintf('%s:%s', $this->username, $this->password));
        
        $context = stream_context_create([
            'http' => [
                'header' => 'Authorization: Basic ' . $auth,
                'timeout' => 30,
            ],
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if (false === $response) {
            $error = error_get_last();
            throw ApiConnectionException::forEndpoint($url, $error['message'] ?? null);
        }
        
        return $response;
    }

    private function parseJsonResponse(string $response): array
    {
        try {
            $data = json_decode(
                json: $response,
                associative: true,
                depth: 16,
                flags: JSON_THROW_ON_ERROR
            );
            
            return $data ?? [];
        } catch (JsonException $e) {
            throw InvalidApiResponseException::forInvalidJson($response, $e->getMessage());
        }
    }
}
