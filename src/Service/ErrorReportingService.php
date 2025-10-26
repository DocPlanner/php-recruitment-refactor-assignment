<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;
use Psr\Log\LoggerInterface;

class ErrorReportingService
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function shouldReportError(): bool
    {
        return !$this->isSunday();
    }

    public function reportError(string $message, array $context = []): void
    {
        if (!$this->shouldReportError()) {
            return;
        }
        
        $this->logger->error($message, $context);
    }

    public function reportInfo(string $message, array $context = []): void
    {
        if (!$this->shouldReportError()) {
            return;
        }
        
        $this->logger->info($message, $context);
    }

    private function isSunday(): bool
    {
        return (new DateTime())->format('D') === 'Sun';
    }
}
