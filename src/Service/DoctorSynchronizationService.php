<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Doctor;
use App\Entity\Slot;
use App\Exception\ApiConnectionException;
use App\Exception\DoctorSynchronizationException;
use App\Infrastructure\ApiClient\DoctorApiClientInterface;
use App\Repository\DoctorRepositoryInterface;
use App\Repository\SlotRepositoryInterface;
use DateTime;
use Psr\Log\LoggerInterface;

class DoctorSynchronizationService
{
    public function __construct(
        private DoctorApiClientInterface $apiClient,
        private DoctorRepositoryInterface $doctorRepository,
        private SlotRepositoryInterface $slotRepository,
        private NameNormalizationService $nameNormalizer,
        private ErrorReportingService $errorReporter,
        private LoggerInterface $logger
    ) {
    }

    public function synchronize(): SynchronizationResult
    {
        $result = new SynchronizationResult();
        
        try {
            $doctors = $this->apiClient->getDoctors();
            
            foreach ($doctors as $doctorData) {
                $this->synchronizeDoctor($doctorData, $result);
            }
        } catch (ApiConnectionException $e) {
            $this->logger->error('Failed to fetch doctors from API', ['exception' => $e]);
            $result->addError('API connection failed: ' . $e->getMessage());
        }
        
        return $result;
    }

    private function synchronizeDoctor(array $doctorData, SynchronizationResult $result): void
    {
        try {
            $doctor = $this->createOrUpdateDoctor($doctorData);
            $this->synchronizeDoctorSlots($doctor, $result);
            
            $result->addSuccess((string)$doctorData['id']);
        } catch (DoctorSynchronizationException $e) {
            $this->handleDoctorError($doctorData, $e, $result);
        }
    }

    private function createOrUpdateDoctor(array $data): Doctor
    {
        $this->validateDoctorData($data);
        
        $id = (string)$data['id'];
        $normalizedName = $this->nameNormalizer->normalize($data['name']);
        
        $doctor = $this->doctorRepository->findById($id)
            ?? new Doctor($id, $normalizedName);
            
        $doctor->setName($normalizedName);
        $doctor->clearError();
        
        $this->doctorRepository->save($doctor);
        
        return $doctor;
    }

    private function synchronizeDoctorSlots(Doctor $doctor, SynchronizationResult $result): void
    {
        try {
            $slotsData = $this->apiClient->getDoctorSlots((int)$doctor->getId());
            
            foreach ($slotsData as $slotData) {
                $this->processSlot($slotData, (int)$doctor->getId());
            }
        } catch (ApiConnectionException $e) {
            // Mark doctor with error if slots cannot be fetched
            $doctor->markError();
            $this->doctorRepository->save($doctor);
            
            $this->errorReporter->reportInfo(
                'Error fetching slots for doctor',
                ['doctorId' => $doctor->getId(), 'exception' => $e->getMessage()]
            );
        }
    }

    private function processSlot(array $slotData, int $doctorId): void
    {
        $this->validateSlotData($slotData);
        
        $start = new DateTime($slotData['start']);
        $end = new DateTime($slotData['end']);
        
        $existingSlot = $this->slotRepository->findByDoctorAndTime($doctorId, $start);
        
        if ($existingSlot) {
            if ($existingSlot->isStale()) {
                $existingSlot->setEnd($end);
                $this->slotRepository->save($existingSlot);
            }
        } else {
            $newSlot = new Slot($doctorId, $start, $end);
            $this->slotRepository->save($newSlot);
        }
    }

    private function handleDoctorError(array $doctorData, DoctorSynchronizationException $e, SynchronizationResult $result): void
    {
        $doctorId = $doctorData['id'] ?? 'unknown';
        
        $this->errorReporter->reportError(
            'Failed to synchronize doctor',
            [
                'doctorId' => $doctorId,
                'doctorName' => $doctorData['name'] ?? 'unknown',
                'exception' => $e->getMessage()
            ]
        );
        
        $result->addError("Doctor {$doctorId}: " . $e->getMessage());
    }

    private function validateDoctorData(array $data): void
    {
        if (!isset($data['id'], $data['name'])) {
            $missing = [];
            if (!isset($data['id'])) $missing[] = 'id';
            if (!isset($data['name'])) $missing[] = 'name';
            
            throw new DoctorSynchronizationException(
                'Missing required doctor fields: ' . implode(', ', $missing)
            );
        }
    }

    private function validateSlotData(array $data): void
    {
        if (!isset($data['start'], $data['end'])) {
            $missing = [];
            if (!isset($data['start'])) $missing[] = 'start';
            if (!isset($data['end'])) $missing[] = 'end';
            
            throw new DoctorSynchronizationException(
                'Missing required slot fields: ' . implode(', ', $missing)
            );
        }
        
        if (!strtotime($data['start']) || !strtotime($data['end'])) {
            throw new DoctorSynchronizationException(
                'Invalid datetime format in slot data'
            );
        }
    }
}
