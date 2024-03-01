<?php
declare(strict_types=1);

namespace App;

use App\Entity\Doctor;
use App\Entity\Slot;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use JsonException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class DoctorSlotsSynchronizer
{
    protected const ENDPOINT = 'http://localhost:2137/api/doctors';
    protected const USERNAME = 'docplanner';
    protected const PASSWORD = 'docplanner';

    protected EntityRepository $repository;
    protected EntityRepository $slots;
    protected Logger $logger;

    public function __construct(EntityManagerInterface $em, string $logFile = 'php://stderr')
    {
        $this->repository = $em->getRepository(Doctor::class);
        $this->slots = $em->getRepository(Slot::class);
        $this->logger = new Logger('logger', [new StreamHandler($logFile)]);
    }

    /**
     * @throws JsonException
     */
    public function synchronizeDoctorSlots(): void
    {
        $doctors = $this->getJsonDecode($this->getDoctors());

        foreach ($doctors as $doctor) {
            $name = $this->normalizeName($doctor['name']);
            /** @var Doctor $entity */
            $entity = $this->repository->find($doctor['id']) ?? new Doctor((string)$doctor['id'], $name);
            $entity->setName($name);
            $entity->clearError();
            $this->save($entity);

            foreach ($this->fetchDoctorSlots($doctor['id']) as $slot) {
                if (false === $slot) {
                    $entity->markError();
                    $this->save($entity);
                } else {
                    $this->save($slot);
                }
            }
        }
    }

    /**
     * @throws JsonException
     */
    protected function getJsonDecode(string|bool $json): mixed
    {
        return json_decode(
            json: false === $json ? '' : $json,
            associative: true,
            depth: 16,
            flags: JSON_THROW_ON_ERROR,
        );
    }

    protected function getDoctors(): string
    {
        return $this->fetchData(self::ENDPOINT);
    }

    protected function fetchData(string $url): string|false
    {
        $auth = base64_encode(
            sprintf(
                '%s:%s',
                self::USERNAME,
                self::PASSWORD,
            ),
        );

        return @file_get_contents(
            filename: $url,
            context: stream_context_create(
                [
                    'http' => [
                        'header' => 'Authorization: Basic ' . $auth,
                    ],
                ],
            ),
        );
    }

    protected function normalizeName(string $fullName): string
    {
        [, $surname] = explode(' ', $fullName);

        /** @see https://www.youtube.com/watch?v=PUhU3qCf0Nk */
        if (0 === stripos($surname, "o'")) {
            return ucwords($fullName, ' \'');
        }

        return ucwords($fullName);
    }

    protected function save(Doctor|Slot $entity): void
    {
        $em = $this->repository->createQueryBuilder('alias')->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    protected function fetchDoctorSlots(int $id): iterable
    {
        try {
            $slots = $this->getJsonDecode($this->getSlots($id));
            yield from $this->parseSlots($slots, $id);
        } catch (JsonException) {
            if ($this->shouldReportErrors()) {
                $this->logger->info('Error fetching slots for doctor', ['doctorId' => $id]);
            }
            yield false;
        }
    }

    protected function getSlots(int $id): string|false
    {
        return $this->fetchData(self::ENDPOINT . '/' . $id . '/slots');
    }

    protected function parseSlots(mixed $slots, int $id): iterable
    {
        foreach ($slots as $slot) {
            $start = new DateTime($slot['start']);
            $end = new DateTime($slot['end']);

            /** @var Slot $entity */
            $entity = $this->slots->findOneBy(['doctorId' => $id, 'start' => $start])
                ?: new Slot($id, $start, $end);

            if ($entity->isStale()) {
                $entity->setEnd($end);
            }

            yield $entity;
        }
    }

    protected function shouldReportErrors(): bool
    {
        return (new DateTime())->format('D') !== 'Sun';
    }
}
