<?php

declare(strict_types=1);

namespace App\Exception;

class InvalidApiResponseException extends DoctorSynchronizationException
{
    public static function forInvalidJson(string $response, string $jsonError): self
    {
        return new self(
            sprintf('Invalid JSON response: %s. Response: %s', $jsonError, substr($response, 0, 100))
        );
    }
    
    public static function forMissingFields(array $missingFields): self
    {
        return new self(
            sprintf('API response missing required fields: %s', implode(', ', $missingFields))
        );
    }
}
