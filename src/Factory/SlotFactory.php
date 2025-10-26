<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Slot;
use App\Exception\InvalidApiResponseException;
use DateTime;

class SlotFactory
{
    public function createFromApiData(array $apiData, int $doctorId): Slot
    {
        $this->validateApiData($apiData);
        
        $start = new DateTime($apiData['start']);
        $end = new DateTime($apiData['end']);
        
        return new Slot($doctorId, $start, $end);
    }

    private function validateApiData(array $data): void
    {
        $missingFields = [];
        
        if (!isset($data['start'])) {
            $missingFields[] = 'start';
        }
        
        if (!isset($data['end'])) {
            $missingFields[] = 'end';
        }
        
        if (!empty($missingFields)) {
            throw InvalidApiResponseException::forMissingFields($missingFields);
        }
        
        if (!strtotime($data['start']) || !strtotime($data['end'])) {
            throw new InvalidApiResponseException('Invalid datetime format in slot data');
        }
    }
}
