<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\ErrorReportingService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ErrorReportingServiceTest extends TestCase
{
    private LoggerInterface $logger;
    private ErrorReportingService $service;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->service = new ErrorReportingService($this->logger);
    }

    public function testShouldReportErrorOnWeekdays(): void
    {
        // This test assumes it's not Sunday when run
        // In a real project, you'd inject a clock service to control time
        $currentDay = (new \DateTime())->format('D');
        
        if ($currentDay !== 'Sun') {
            $this->assertTrue($this->service->shouldReportError());
        } else {
            $this->assertFalse($this->service->shouldReportError());
        }
    }

    public function testReportErrorCallsLogger(): void
    {
        // Skip this test on Sundays since errors aren't reported
        $currentDay = (new \DateTime())->format('D');
        if ($currentDay === 'Sun') {
            $this->markTestSkipped('Test skipped on Sundays due to business rule');
        }

        $this->logger->expects($this->once())
            ->method('error')
            ->with('Test error message', ['context' => 'test']);

        $this->service->reportError('Test error message', ['context' => 'test']);
    }

    public function testReportInfoCallsLogger(): void
    {
        // Skip this test on Sundays since errors aren't reported
        $currentDay = (new \DateTime())->format('D');
        if ($currentDay === 'Sun') {
            $this->markTestSkipped('Test skipped on Sundays due to business rule');
        }

        $this->logger->expects($this->once())
            ->method('info')
            ->with('Test info message', ['context' => 'test']);

        $this->service->reportInfo('Test info message', ['context' => 'test']);
    }
}
