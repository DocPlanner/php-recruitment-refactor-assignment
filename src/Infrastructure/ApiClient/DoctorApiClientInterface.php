<?php

declare(strict_types=1);

namespace App\Infrastructure\ApiClient;

interface DoctorApiClientInterface
{
    /**
     * Fetch list of all doctors from external API
     *
     * @return array Array of doctor data with 'id' and 'name' keys
     * @throws ApiConnectionException
     */
    public function getDoctors(): array;
    
    /**
     * Fetch appointment slots for specific doctor
     *
     * @param int $doctorId
     * @return array Array of slot data with 'start' and 'end' keys
     * @throws ApiConnectionException
     */
    public function getDoctorSlots(int $doctorId): array;
}
