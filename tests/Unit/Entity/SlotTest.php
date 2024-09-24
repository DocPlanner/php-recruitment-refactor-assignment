<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\Doctor;
use App\Entity\Slot;
use DateTime;
use PHPUnit\Framework\TestCase;

class SlotTest extends TestCase
{
    public function testSlotInitialization(): void
    {
        $doctor = $this->createMock(Doctor::class);
        $start = new DateTime('2023-10-01T09:00:00');
        $end = new DateTime('2023-10-01T10:00:00');

        $slot = new Slot($doctor, $start, $end);

        $this->assertSame($doctor, $slot->getDoctor());
        $this->assertEquals($start, $slot->getStart());
        $this->assertEquals($end, $slot->getEnd());
        $this->assertInstanceOf(DateTime::class, $slot->getCreatedAt());
    }

    public function testSetAndGetStart(): void
    {
        $doctor = $this->createMock(Doctor::class);
        $start = new DateTime('2023-10-01T09:00:00');
        $newStart = new DateTime('2023-10-01T11:00:00');

        $slot = new Slot($doctor, $start, new DateTime('2023-10-01T10:00:00'));
        $slot->setStart($newStart);

        $this->assertEquals($newStart, $slot->getStart());
    }

    public function testSetAndGetEnd(): void
    {
        $doctor = $this->createMock(Doctor::class);
        $end = new DateTime('2023-10-01T10:00:00');
        $newEnd = new DateTime('2023-10-01T12:00:00');

        $slot = new Slot($doctor, new DateTime('2023-10-01T09:00:00'), $end);
        $slot->setEnd($newEnd);

        $this->assertEquals($newEnd, $slot->getEnd());
    }

    public function testSetAndGetDoctor(): void
    {
        $doctor = $this->createMock(Doctor::class);
        $newDoctor = $this->createMock(Doctor::class);

        $slot = new Slot($doctor, new DateTime(), new DateTime());
        $slot->setDoctor($newDoctor);

        $this->assertSame($newDoctor, $slot->getDoctor());
    }

    public function testIsStaleReturnsFalseWhenNotStale(): void
    {
        $doctor = $this->createMock(Doctor::class);
        $slot = new Slot($doctor, new DateTime(), new DateTime());

        // The slot was just created, so it should not be stale
        $this->assertFalse($slot->isStale());
    }

    public function testIsStaleReturnsTrueWhenStale(): void
    {
        $doctor = $this->createMock(Doctor::class);
        $slot = new Slot($doctor, new DateTime(), new DateTime());

        // Simulate that the slot was created over 5 minutes ago
        $reflection = new \ReflectionClass($slot);
        $property = $reflection->getProperty('createdAt');
        $property->setAccessible(true);
        $property->setValue($slot, (new DateTime())->modify('-10 minutes'));

        $this->assertTrue($slot->isStale());
    }
}
