<?php

declare(strict_types=1);

namespace Tests\Unit\Service;

use App\Entity\Doctor;
use App\Entity\Slot;
use App\Service\StaticDoctorSlotsSynchronizer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class StaticDoctorSlotsSynchronizerTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private StaticDoctorSlotsSynchronizer $synchronizer;
    private ObjectRepository $doctorRepository;
    private ObjectRepository $slotRepository;

    protected function setUp(): void
    {
        // Create mocks for the EntityManager and Logger
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        // Create mocks for the repositories
        $this->doctorRepository = $this->createMock(ObjectRepository::class);
        $this->slotRepository = $this->createMock(ObjectRepository::class);

        // Configure the EntityManager to return the mocked repositories
        $this->entityManager->method('getRepository')
            ->willReturnMap([
                [Doctor::class, $this->doctorRepository],
                [Slot::class, $this->slotRepository],
            ]);

        // Initialize the synchronizer
        $this->synchronizer = new StaticDoctorSlotsSynchronizer(
            $this->entityManager,
            $this->logger
        );
    }

    public function testGetDoctorsReturnsStaticData(): void
    {
        // Use Reflection to access the protected getDoctors() method
        $reflection = new \ReflectionClass($this->synchronizer);
        $method = $reflection->getMethod('getDoctors');
        $method->setAccessible(true);

        $doctorsJson = $method->invoke($this->synchronizer);
        $doctors = json_decode($doctorsJson, true);

        // Assertions
        $this->assertIsArray($doctors);
        $this->assertCount(30, $doctors);

        $firstDoctor = $doctors[0];
        $this->assertEquals(0, $firstDoctor['id']);
        $this->assertEquals('Adoring Shtern', $firstDoctor['name']);
    }

    public function testSynchronizeDoctorSlots(): void
    {
        // Arrange
        // Configure the repositories
        $this->doctorRepository->method('find')
            ->willReturn(null);

        $this->slotRepository->method('findOneBy')
            ->willReturn(null);

        // Expect that persist() is called for Doctors and Slots
        $this->entityManager->expects($this->atLeastOnce())
            ->method('persist');

        $this->entityManager->expects($this->any())
            ->method('flush');

        // Act
        $this->synchronizer->synchronizeDoctorSlots();

        // Assert
        // If no exceptions are thrown, the test passes
        $this->assertTrue(true);
    }
}
