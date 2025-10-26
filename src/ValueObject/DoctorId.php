<?php

declare(strict_types=1);

namespace App\ValueObject;

use InvalidArgumentException;

final class DoctorId
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Doctor ID cannot be empty');
        }
        
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('Doctor ID must be numeric');
        }
        
        $this->value = $value;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(DoctorId $other): bool
    {
        return $this->value === $other->value;
    }
}
