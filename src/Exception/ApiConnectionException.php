<?php

declare(strict_types=1);

namespace App\Exception;

class ApiConnectionException extends DoctorSynchronizationException
{
    public static function forEndpoint(string $endpoint, ?string $reason = null): self
    {
        $message = sprintf('Failed to connect to API endpoint: %s', $endpoint);
        if ($reason) {
            $message .= sprintf('. Reason: %s', $reason);
        }
        
        return new self($message);
    }
}
