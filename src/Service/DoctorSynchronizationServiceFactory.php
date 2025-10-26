<?php

declare(strict_types=1);

namespace App\Service;

use App\Infrastructure\ApiClient\DoctorApiClientInterface;
use App\Infrastructure\ApiClient\HttpDoctorApiClient;
use App\Infrastructure\ApiClient\StaticDoctorApiClient;
use App\Repository\Doctrine\DoctorRepository;
use App\Repository\Doctrine\SlotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Factory to create DoctorSynchronizationService with all its dependencies
 */
class DoctorSynchronizationServiceFactory
{
    public static function createWithHttpClient(
        EntityManagerInterface $entityManager,
        string $apiBaseUrl = 'http://localhost:2137',
        string $username = 'docplanner',
        string $password = 'docplanner',
        string $logFile = 'php://stderr'
    ): DoctorSynchronizationService {
        $apiClient = new HttpDoctorApiClient($apiBaseUrl, $username, $password);
        
        return self::createService($entityManager, $apiClient, $logFile);
    }

    public static function createWithStaticClient(
        EntityManagerInterface $entityManager,
        string $logFile = 'php://stderr'
    ): DoctorSynchronizationService {
        $apiClient = new StaticDoctorApiClient();
        
        return self::createService($entityManager, $apiClient, $logFile);
    }

    private static function createService(
        EntityManagerInterface $entityManager,
        DoctorApiClientInterface $apiClient,
        string $logFile
    ): DoctorSynchronizationService {
        $logger = new Logger('doctor_sync', [new StreamHandler($logFile)]);
        
        $doctorRepository = new DoctorRepository($entityManager);
        $slotRepository = new SlotRepository($entityManager);
        $nameNormalizer = new NameNormalizationService();
        $errorReporter = new ErrorReportingService($logger);

        return new DoctorSynchronizationService(
            $apiClient,
            $doctorRepository,
            $slotRepository,
            $nameNormalizer,
            $errorReporter,
            $logger
        );
    }
}
