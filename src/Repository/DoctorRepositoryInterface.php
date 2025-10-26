<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Doctor;

interface DoctorRepositoryInterface
{
    public function findById(string $id): ?Doctor;
    
    public function save(Doctor $doctor): void;
    
    /** @return Doctor[] */
    public function findAll(): array;
    
    /** @return Doctor[] */
    public function findByName(string $namePattern): array;
    
    /** @return Doctor[] */
    public function findWithErrors(): array;
}
