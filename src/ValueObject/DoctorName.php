<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Exception\InvalidNameException;

final class DoctorName
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        
        if (empty($trimmed)) {
            throw InvalidNameException::empty();
        }
        
        if (strlen($value) > 255) {
            throw InvalidNameException::tooLong();
        }
        
        if (substr_count($value, ' ') < 1) {
            throw InvalidNameException::tooShort();
        }
        
        $this->value = $value;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(DoctorName $other): bool
    {
        return $this->value === $other->value;
    }

    public static function fromRawName(string $rawName): self
    {
        $normalized = self::normalize($rawName);
        return new self($normalized);
    }

    private static function normalize(string $fullName): string
    {
        $parts = explode(' ', $fullName, 2);
        if (count($parts) < 2) {
            return ucwords($fullName);
        }
        
        [, $surname] = $parts;
        
        // Special handling for Irish surnames
        if (0 === stripos($surname, "o'")) {
            return ucwords($fullName, ' \'');
        }
        
        return ucwords($fullName);
    }
}
