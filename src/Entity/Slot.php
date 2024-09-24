<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="slots")
 * @ORM\HasLifecycleCallbacks()
 */
class Slot
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Doctor", inversedBy="slots")
     * @ORM\JoinColumn(name="doctor_id", referencedColumnName="id", nullable=false)
     */
    private Doctor $doctor;

    /** @ORM\Column(type="datetime") */
    private DateTime $start;

    /** @ORM\Column(type="datetime") */
    private DateTime $end;

    /** @ORM\Column(type="datetime") */
    private DateTime $createdAt;

    public function __construct(Doctor $doctor, DateTime $start, DateTime $end)
    {
        $this->doctor = $doctor;
        $this->start = $start;
        $this->end = $end;
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function setDoctor(Doctor $doctor): void
    {
        $this->doctor = $doctor;
    }

    public function getDoctor(): Doctor
    {
        return $this->doctor;
    }

    public function setStart(DateTime $start): void
    {
        $this->start = $start;
    }

    public function getStart(): DateTime
    {
        return $this->start;
    }

    public function setEnd(DateTime $end): void
    {
        $this->end = $end;
    }

    public function getEnd(): DateTime
    {
        return $this->end;
    }

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist(): void
    {
        $this->createdAt = new DateTime();
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function isStale(): bool
    {
        $now = new DateTime();
        $interval = $now->getTimestamp() - $this->createdAt->getTimestamp();

        return $interval > 300; // 300 segundos = 5 minutos
    }
}
