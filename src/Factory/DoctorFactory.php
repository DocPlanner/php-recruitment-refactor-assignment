<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Doctor;
use App\Exception\InvalidApiResponseException;
use App\Service\NameNormalizationService;

class DoctorFactory
{
    public function __construct(private NameNormalizationService $nameNormalizer)
    {
    }

    public function createFromApiData(array $apiData): Doctor
    {
        $this->validateApiData($apiData);
        
        $id = (string)$apiData['id'];
        $normalizedName = $this->nameNormalizer->normalize($apiData['name']);
        
        return new Doctor($id, $normalizedName);
    }

    private function validateApiData(array $data): void
    {
        $missingFields = [];
        
        if (!isset($data['id'])) {
            $missingFields[] = 'id';
        }
        
        if (!isset($data['name'])) {
            $missingFields[] = 'name';
        }
        
        if (!empty($missingFields)) {
            throw InvalidApiResponseException::forMissingFields($missingFields);
        }
    }
}
