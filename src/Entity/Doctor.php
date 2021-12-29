<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="doctor")
 */
final class Doctor
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private string $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $error = false;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function markError(): void
    {
        $this->error = true;
    }

    public function clearError(): void
    {
        $this->error = false;
    }

    public function hasError(): bool
    {
        return $this->error;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
