<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'T_TIMESLOT_SLT')]
class TimeSlot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'SLT_ID', type: 'smallint')]
    private ?int $id = null;

    #[ORM\Column(name: 'SLT_DATE_BEGIN', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateBegin = null;

    #[ORM\Column(name: 'SLT_DATE_END', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnd = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateBegin(): ?\DateTimeInterface
    {
        return $this->dateBegin;
    }

    public function setDateBegin(\DateTimeInterface $dateBegin): self
    {
        $this->dateBegin = $dateBegin;
        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }
}
