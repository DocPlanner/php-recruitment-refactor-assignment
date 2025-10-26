<?php

declare(strict_types=1);

namespace App\Infrastructure\ApiClient;

use App\Exception\InvalidApiResponseException;
use JsonException;

class StaticDoctorApiClient implements DoctorApiClientInterface
{
    private const STATIC_DOCTORS_DATA = '[
        {
            "id": 0,
            "name": "Adoring Shtern"
        },
        {
            "id": 1,
            "name": "Brave Ramanujan"
        },
        {
            "id": 2,
            "name": "Tender Rosalind"
        },
        {
            "id": 3,
            "name": "Beautiful Stonebraker"
        },
        {
            "id": 4,
            "name": "Brave Northcutt"
        },
        {
            "id": 5,
            "name": "Loving Shaw"
        },
        {
            "id": 6,
            "name": "Clever Noyce"
        },
        {
            "id": 7,
            "name": "Hopeful Hopper"
        },
        {
            "id": 8,
            "name": "Boring Curran"
        },
        {
            "id": 9,
            "name": "Vigorous Rhodes"
        },
        {
            "id": 10,
            "name": "Lucid Lehmann"
        },
        {
            "id": 11,
            "name": "Magical Leakey"
        },
        {
            "id": 12,
            "name": "Kind Davinci"
        },
        {
            "id": 13,
            "name": "Friendly Swirles"
        },
        {
            "id": 14,
            "name": "Elastic Pare"
        },
        {
            "id": 15,
            "name": "Kind Bhabha"
        },
        {
            "id": 16,
            "name": "Confident Beaver"
        },
        {
            "id": 17,
            "name": "Gracious Solomon"
        },
        {
            "id": 18,
            "name": "Funny Hopper"
        },
        {
            "id": 19,
            "name": "Sweet Meninsky"
        },
        {
            "id": 20,
            "name": "Exciting Bartik"
        },
        {
            "id": 21,
            "name": "Boring Sutherland"
        },
        {
            "id": 22,
            "name": "Vibrant Jepsen"
        },
        {
            "id": 23,
            "name": "Wizardly Dhawan"
        },
        {
            "id": 24,
            "name": "Infallible Shamir"
        },
        {
            "id": 25,
            "name": "Dazzling Meitner"
        },
        {
            "id": 26,
            "name": "Elated Payne"
        },
        {
            "id": 27,
            "name": "Festive Villani"
        },
        {
            "id": 28,
            "name": "Affectionate Yonath"
        },
        {
            "id": 29,
            "name": "Eloquent Brown"
        }
    ]';

    public function getDoctors(): array
    {
        try {
            $data = json_decode(self::STATIC_DOCTORS_DATA, true, 16, JSON_THROW_ON_ERROR);
            return $data ?? [];
        } catch (JsonException $e) {
            throw InvalidApiResponseException::forInvalidJson(self::STATIC_DOCTORS_DATA, $e->getMessage());
        }
    }

    public function getDoctorSlots(int $doctorId): array
    {
        // Return empty array for static client - no slots data available
        // In a real implementation this could return mock data for testing
        return [];
    }
}
