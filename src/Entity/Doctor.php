<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="doctors")
 */
class Doctor
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private int $id;

    /** @ORM\Column(type="string", length=255) */
    private string $name;

    /** @ORM\Column(type="boolean") */
    private bool $hasError = false;

    /**
     * @ORM\OneToMany(targetEntity="Slot", mappedBy="doctor", cascade={"persist", "remove"})
     */
    private $slots;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->slots = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function markError(): void
    {
        $this->hasError = true;
    }

    public function clearError(): void
    {
        $this->hasError = false;
    }

    public function hasError(): bool
    {
        return $this->hasError;
    }

    public function addSlot(Slot $slot): void
    {
        $this->slots[] = $slot;
        $slot->setDoctor($this);
    }

    public function getSlots()
    {
        return $this->slots;
    }
}
