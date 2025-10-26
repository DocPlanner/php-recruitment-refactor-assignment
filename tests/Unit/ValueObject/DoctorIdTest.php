<?php

declare(strict_types=1);

namespace App\Tests\Unit\ValueObject;

use App\ValueObject\DoctorId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DoctorIdTest extends TestCase
{
    public function testCreateValidDoctorId(): void
    {
        $doctorId = new DoctorId('123');
        
        $this->assertEquals('123', $doctorId->toString());
    }

    public function testCreateDoctorIdWithStringNumber(): void
    {
        $doctorId = new DoctorId('456');
        
        $this->assertEquals('456', $doctorId->toString());
    }

    public function testThrowsExceptionForEmptyId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Doctor ID cannot be empty');
        
        new DoctorId('');
    }

    public function testThrowsExceptionForNonNumericId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Doctor ID must be numeric');
        
        new DoctorId('abc');
    }

    public function testEquals(): void
    {
        $id1 = new DoctorId('123');
        $id2 = new DoctorId('123');
        $id3 = new DoctorId('456');
        
        $this->assertTrue($id1->equals($id2));
        $this->assertFalse($id1->equals($id3));
    }
}
