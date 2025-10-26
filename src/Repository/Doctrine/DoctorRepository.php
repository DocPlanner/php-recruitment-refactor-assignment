<?php

declare(strict_types=1);

namespace App\Repository\Doctrine;

use App\Entity\Doctor;
use App\Repository\DoctorRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DoctorRepository implements DoctorRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Doctor::class);
    }

    public function findById(string $id): ?Doctor
    {
        return $this->repository->find($id);
    }

    public function save(Doctor $doctor): void
    {
        $this->entityManager->persist($doctor);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function findByName(string $namePattern): array
    {
        return $this->repository->createQueryBuilder('d')
            ->where('d.name LIKE :pattern')
            ->setParameter('pattern', '%' . $namePattern . '%')
            ->getQuery()
            ->getResult();
    }

    public function findWithErrors(): array
    {
        return $this->repository->createQueryBuilder('d')
            ->where('d.error = :hasError')
            ->setParameter('hasError', true)
            ->getQuery()
            ->getResult();
    }
}
