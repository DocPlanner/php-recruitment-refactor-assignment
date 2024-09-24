<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\Doctor;
use App\Entity\Slot;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class DoctorTest extends TestCase
{
    public function testDoctorInitialization(): void
    {
        $doctor = new Doctor(1, 'Rafa Terrero');

        $this->assertEquals(1, $doctor->getId());
        $this->assertEquals('Rafa Terrero', $doctor->getName());
        $this->assertFalse($doctor->hasError());
        $this->assertInstanceOf(ArrayCollection::class, $doctor->getSlots());
        $this->assertCount(0, $doctor->getSlots());
    }

    public function testSetName(): void
    {
        $doctor = new Doctor(1, 'Rafa Terrero');
        $doctor->setName('Silvia Perez');

        $this->assertEquals('Silvia Perez', $doctor->getName());
    }

    public function testMarkAndClearError(): void
    {
        $doctor = new Doctor(1, 'Rafa Terrero');

        $doctor->markError();
        $this->assertTrue($doctor->hasError());

        $doctor->clearError();
        $this->assertFalse($doctor->hasError());
    }

    public function testAddSlot(): void
    {
        $doctor = new Doctor(1, 'Rafa Terrero');
        $slot = $this->createMock(Slot::class);
        $slot->expects($this->once())
            ->method('setDoctor')
            ->with($doctor);

        $doctor->addSlot($slot);

        $this->assertCount(1, $doctor->getSlots());
        $this->assertSame($slot, $doctor->getSlots()->first());
    }

    public function testGetSlots(): void
    {
        $doctor = new Doctor(1, 'Rafa Terrero');
        $slot1 = $this->createMock(Slot::class);
        $slot2 = $this->createMock(Slot::class);

        $doctor->addSlot($slot1);
        $doctor->addSlot($slot2);

        $slots = $doctor->getSlots();

        $this->assertCount(2, $slots);
        $this->assertContains($slot1, $slots);
        $this->assertContains($slot2, $slots);
    }
}
