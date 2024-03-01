<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="slot")
 */
final class Slot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private string $id;

    /**
     * @ORM\Column(type="integer")
     */
    private int $doctorId;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $start;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $end;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdAt;

    public function __construct(int $doctorId, DateTime $start, DateTime $end)
    {
        $this->doctorId = $doctorId;
        $this->start = $start;
        $this->end = $end;
        $this->createdAt = new DateTime();
    }

    public function getStart(): DateTime
    {
        return $this->start;
    }

    public function setEnd(DateTime $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function isStale(): bool
    {
        return $this->createdAt < new DateTime('5 minutes ago');
    }
}
