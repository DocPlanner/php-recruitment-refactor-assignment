<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Slot;
use DateTime;

interface SlotRepositoryInterface
{
    public function findById(int $id): ?Slot;
    
    public function save(Slot $slot): void;
    
    public function findByDoctorAndTime(int $doctorId, DateTime $start): ?Slot;
    
    /** @return Slot[] */
    public function findByDoctor(int $doctorId): array;
    
    /** @return Slot[] */
    public function findStaleSlots(DateTime $before): array;
}
