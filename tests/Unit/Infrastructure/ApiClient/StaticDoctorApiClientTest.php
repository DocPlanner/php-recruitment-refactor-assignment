<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\ApiClient;

use App\Infrastructure\ApiClient\StaticDoctorApiClient;
use PHPUnit\Framework\TestCase;

class StaticDoctorApiClientTest extends TestCase
{
    private StaticDoctorApiClient $client;

    protected function setUp(): void
    {
        $this->client = new StaticDoctorApiClient();
    }

    public function testGetDoctorsReturnsArrayOfDoctors(): void
    {
        $doctors = $this->client->getDoctors();
        
        $this->assertIsArray($doctors);
        $this->assertNotEmpty($doctors);
        
        foreach ($doctors as $doctor) {
            $this->assertArrayHasKey('id', $doctor);
            $this->assertArrayHasKey('name', $doctor);
            $this->assertIsInt($doctor['id']);
            $this->assertIsString($doctor['name']);
        }
    }

    public function testGetDoctorsReturnsExpectedFirstDoctor(): void
    {
        $doctors = $this->client->getDoctors();
        
        $firstDoctor = $doctors[0];
        $this->assertEquals(0, $firstDoctor['id']);
        $this->assertEquals('Adoring Shtern', $firstDoctor['name']);
    }

    public function testGetDoctorSlotsReturnsEmptyArray(): void
    {
        $slots = $this->client->getDoctorSlots(1);
        
        $this->assertIsArray($slots);
        $this->assertEmpty($slots);
    }
}
