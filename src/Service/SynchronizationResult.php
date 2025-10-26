<?php

declare(strict_types=1);

namespace App\Service;

class SynchronizationResult
{
    private array $successfulDoctors = [];
    private array $errors = [];
    private int $processedCount = 0;

    public function addSuccess(string $doctorId): void
    {
        $this->successfulDoctors[] = $doctorId;
        $this->processedCount++;
    }

    public function addError(string $error): void
    {
        $this->errors[] = $error;
        $this->processedCount++;
    }

    public function getSuccessfulDoctors(): array
    {
        return $this->successfulDoctors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getProcessedCount(): int
    {
        return $this->processedCount;
    }

    public function getSuccessCount(): int
    {
        return count($this->successfulDoctors);
    }

    public function getErrorCount(): int
    {
        return count($this->errors);
    }

    public function isSuccessful(): bool
    {
        return empty($this->errors);
    }

    public function hasPartialSuccess(): bool
    {
        return !empty($this->successfulDoctors) && !empty($this->errors);
    }
}
