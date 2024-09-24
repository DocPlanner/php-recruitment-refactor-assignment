<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Doctor;
use App\Entity\Slot;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use JsonException;
use Psr\Log\LoggerInterface;

class DoctorSlotsSynchronizer
{
    protected const MAX_RETRIES = 3;
    protected const RETRY_DELAY = 1000;

    protected EntityManagerInterface $entityManager;
    protected ObjectRepository $doctorRepository;
    protected ObjectRepository $slotRepository;
    protected LoggerInterface $logger;

    private string $endpoint;
    private string $username;
    private string $password;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        string $endpoint,
        string $username,
        string $password
    ) {
        $this->entityManager = $entityManager;
        $this->doctorRepository = $entityManager->getRepository(Doctor::class);
        $this->slotRepository = $entityManager->getRepository(Slot::class);
        $this->logger = $logger;
        $this->endpoint = rtrim($endpoint, '/');
        $this->username = $username;
        $this->password = $password;
    }

    public function synchronizeDoctorSlots(): void
    {
        try {
            $doctors = $this->getJsonDecode($this->getDoctors());
            $this->logger->info('Doctor list ok.');
        } catch (JsonException $e) {
            $this->logger->error('Error obtaining doctor list.', ['exception' => $e]);
            return;
        }

        foreach ($doctors as $doctorData) {
            $this->processDoctor($doctorData);
        }
    }

    protected function processDoctor(array $doctorData): void
    {
        $name = $this->normalizeName($doctorData['name']);
        $doctorId = (int)$doctorData['id'];

        $this->logger->info('Processing doctor.', ['doctorId' => $doctorId, 'name' => $name]);

        /** @var Doctor|null $doctor */
        $doctor = $this->doctorRepository->find($doctorId);

        if (!$doctor) {
            $doctor = new Doctor($doctorId, $name);
            $this->logger->info('Doctor created.', ['doctorId' => $doctorId]);
        } else {
            $doctor->setName($name);
            $this->logger->info('Doctor updated.', ['doctorId' => $doctorId]);
        }

        $doctor->clearError();
        $this->save($doctor);

        foreach ($this->fetchDoctorSlots($doctor) as $slot) {
            if ($slot === false) {
                $doctor->markError();
                $this->save($doctor);
            } else {
                $this->save($slot);
            }
        }
    }

    /**
     * @return iterable<Slot|false>
     */
    protected function fetchDoctorSlots(Doctor $doctor): iterable
    {
        $doctorId = $doctor->getId();

        try {
            $slotsData = $this->getJsonDecode($this->getSlots($doctorId));
            $this->logger->info('Slots obtained for the doctor.', ['doctorId' => $doctorId]);
            yield from $this->parseSlots($slotsData, $doctor);
        } catch (JsonException $e) {
            if ($this->shouldReportErrors()) {
                $this->logger->error('Error to get slots for the doctor.', [
                    'doctorId'  => $doctorId,
                    'exception' => $e,
                ]);
            }
            yield false;
        }
    }

    /**
     * @param array $slotsData
     * @param Doctor $doctor
     * @return iterable<Slot>
     */
    protected function parseSlots(array $slotsData, Doctor $doctor): iterable
    {
        foreach ($slotsData as $slotData) {
            $start = new DateTime($slotData['start']);
            $end = new DateTime($slotData['end']);

            $this->logger->info('Processing slot.', [
                'doctorId' => $doctor->getId(),
                'start'    => $start->format(DateTime::ATOM),
                'end'      => $end->format(DateTime::ATOM),
            ]);

            /** @var Slot|null $slot */
            $slot = $this->slotRepository->findOneBy([
                'doctor' => $doctor,
                'start'  => $start,
            ]);

            if (!$slot) {
                $slot = new Slot($doctor, $start, $end);
                $this->logger->info('Slot created.', ['doctorId' => $doctor->getId()]);
            } elseif ($slot->isStale()) {
                $slot->setEnd($end);
                $this->logger->info('Slot updated because is deprecated.', ['doctorId' => $doctor->getId()]);
            }

            yield $slot;
        }
    }

    protected function retry(callable $function, int $maxRetries = self::MAX_RETRIES, int $delay = self::RETRY_DELAY)
    {
        $attempts = 0;
        do {
            try {
                return $function();
            } catch (\Exception $e) {
                $attempts++;
                $this->logger->warning('Fail... retrying...', [
                    'attempt'    => $attempts,
                    'maxRetries' => $maxRetries,
                    'exception'  => $e,
                ]);
                if ($attempts >= $maxRetries) {
                    $this->logger->error('Max number of retrying. Failed operation.', [
                        'exception' => $e,
                    ]);
                    throw $e;
                }
                usleep($delay * 1000);
            }
        } while ($attempts < $maxRetries);
    }

    protected function fetchData(string $url): string|false
    {
        $auth = base64_encode(sprintf('%s:%s', $this->username, $this->password));
        $context = stream_context_create([
            'http' => [
                'header' => 'Authorization: Basic ' . $auth,
            ],
        ]);

        return $this->retry(function () use ($url, $context) {
            $response = @file_get_contents($url, false, $context);
            if ($response === false) {
                $error = error_get_last();
                throw new \RuntimeException("Error getting data from {$url}: " . $error['message']);
            }
            $this->logger->info('Data OK.', ['url' => $url]);
            return $response;
        });
    }

    protected function getDoctors(): string|false
    {
        return $this->fetchData($this->endpoint);
    }

    protected function getSlots(int $doctorId): string|false
    {
        return $this->fetchData("{$this->endpoint}/{$doctorId}/slots");
    }

    /**
     * @throws JsonException
     */
    protected function getJsonDecode(string|false $json): array
    {
        return json_decode(
            $json ?: '',
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    protected function normalizeName(string $fullName): string
    {
        $parts = explode(' ', $fullName);
        $surname = $parts[0] ?? '';

        if (stripos($surname, "o'") === 0) {
            return ucwords($fullName, " '");
        }

        return ucwords($fullName);
    }

    protected function save(Doctor|Slot $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    protected function shouldReportErrors(): bool
    {
        return (new DateTime())->format('D') !== 'Sun';
    }
}
