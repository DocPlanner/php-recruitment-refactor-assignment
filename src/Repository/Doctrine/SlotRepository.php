<?php

declare(strict_types=1);

namespace App\Repository\Doctrine;

use App\Entity\Slot;
use App\Repository\SlotRepositoryInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class SlotRepository implements SlotRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Slot::class);
    }

    public function findById(int $id): ?Slot
    {
        return $this->repository->find($id);
    }

    public function save(Slot $slot): void
    {
        $this->entityManager->persist($slot);
        $this->entityManager->flush();
    }

    public function findByDoctorAndTime(int $doctorId, DateTime $start): ?Slot
    {
        return $this->repository->createQueryBuilder('s')
            ->where('s.doctorId = :doctorId')
            ->andWhere('s.start = :start')
            ->setParameter('doctorId', $doctorId)
            ->setParameter('start', $start)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByDoctor(int $doctorId): array
    {
        return $this->repository->createQueryBuilder('s')
            ->where('s.doctorId = :doctorId')
            ->setParameter('doctorId', $doctorId)
            ->orderBy('s.start', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findStaleSlots(DateTime $before): array
    {
        return $this->repository->createQueryBuilder('s')
            ->where('s.createdAt < :before')
            ->setParameter('before', $before)
            ->getQuery()
            ->getResult();
    }
}
