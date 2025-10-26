<?php

declare(strict_types=1);

namespace App\Tests\Unit\ValueObject;

use App\Exception\InvalidNameException;
use App\ValueObject\DoctorName;
use PHPUnit\Framework\TestCase;

class DoctorNameTest extends TestCase
{
    public function testCreateValidDoctorName(): void
    {
        $name = new DoctorName('John Doe');
        
        $this->assertEquals('John Doe', $name->toString());
    }

    public function testFromRawNameNormalization(): void
    {
        $name = DoctorName::fromRawName('john doe');
        
        $this->assertEquals('John Doe', $name->toString());
    }

    public function testFromRawNameWithIrishSurname(): void
    {
        $name = DoctorName::fromRawName("sean o'malley");
        
        $this->assertEquals("Sean O'Malley", $name->toString());
    }

    public function testThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidNameException::class);
        $this->expectExceptionMessage('Doctor name cannot be empty');
        
        new DoctorName('');
    }

    public function testThrowsExceptionForWhitespaceOnlyName(): void
    {
        $this->expectException(InvalidNameException::class);
        $this->expectExceptionMessage('Doctor name cannot be empty');
        
        new DoctorName('   ');
    }

    public function testThrowsExceptionForTooLongName(): void
    {
        $longName = str_repeat('a', 256);
        
        $this->expectException(InvalidNameException::class);
        $this->expectExceptionMessage('Doctor name is too long (max 255 characters)');
        
        new DoctorName($longName);
    }

    public function testThrowsExceptionForSingleName(): void
    {
        $this->expectException(InvalidNameException::class);
        $this->expectExceptionMessage('Full name must contain both first and last name');
        
        new DoctorName('John');
    }

    public function testEquals(): void
    {
        $name1 = new DoctorName('John Doe');
        $name2 = new DoctorName('John Doe');
        $name3 = new DoctorName('Jane Smith');
        
        $this->assertTrue($name1->equals($name2));
        $this->assertFalse($name1->equals($name3));
    }
}
