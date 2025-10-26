<?php

declare(strict_types=1);

namespace App;

use App\Service\DoctorSynchronizationServiceFactory;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Backward compatibility wrapper for the original DoctorSlotsSynchronizer
 * 
 * @deprecated Use DoctorSynchronizationService directly instead
 */
class LegacyDoctorSlotsSynchronizer
{
    private $modernService;

    public function __construct(EntityManagerInterface $em, string $logFile = 'php://stderr')
    {
        // Trigger deprecation notice
        trigger_error(
            'LegacyDoctorSlotsSynchronizer is deprecated. Use DoctorSynchronizationService instead.',
            E_USER_DEPRECATED
        );

        $this->modernService = DoctorSynchronizationServiceFactory::createWithHttpClient(
            $em,
            logFile: $logFile
        );
    }

    /**
     * @deprecated Use DoctorSynchronizationService::synchronize() instead
     */
    public function synchronizeDoctorSlots(): void
    {
        $result = $this->modernService->synchronize();
        
        // The original method didn't return anything, so we just log the result
        if (!$result->isSuccessful()) {
            error_log(sprintf(
                'Synchronization completed with %d errors out of %d processed doctors',
                $result->getErrorCount(),
                $result->getProcessedCount()
            ));
        }
    }
}
