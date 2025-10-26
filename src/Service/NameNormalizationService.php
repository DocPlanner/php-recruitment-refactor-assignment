<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\InvalidNameException;

class NameNormalizationService
{
    public function normalize(string $fullName): string
    {
        $this->validateName($fullName);
        
        $parts = explode(' ', $fullName, 2);
        if (count($parts) < 2) {
            return ucwords($fullName);
        }
        
        [, $surname] = $parts;
        
        if ($this->isIrishSurname($surname)) {
            return ucwords($fullName, ' \'');
        }
        
        return ucwords($fullName);
    }

    private function isIrishSurname(string $surname): bool
    {
        return 0 === stripos($surname, "o'");
    }

    private function validateName(string $name): void
    {
        if (empty(trim($name))) {
            throw InvalidNameException::empty();
        }
        
        if (substr_count($name, ' ') < 1) {
            throw InvalidNameException::tooShort();
        }
    }
}
