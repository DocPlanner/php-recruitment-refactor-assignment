<?php

declare(strict_types=1);

namespace App\Exception;

class InvalidNameException extends DoctorSynchronizationException
{
    public static function empty(): self
    {
        return new self('Doctor name cannot be empty');
    }
    
    public static function tooShort(): self
    {
        return new self('Full name must contain both first and last name');
    }
    
    public static function tooLong(): self
    {
        return new self('Doctor name is too long (max 255 characters)');
    }
}
