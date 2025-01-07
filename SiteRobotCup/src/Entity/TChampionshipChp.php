<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: "T_CHAMPIONSHIP_CHP")]
class TChampionshipChp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "CHP_ID", type: "smallint")]
    private ?int $id = null;

    #[ORM\Column(name: "CHP_NAME", type: "string", length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(name: "CHP_DATE", type: "datetime")]
    private ?\DateTimeInterface $date = null;

    #[ORM\OneToMany(mappedBy: "championship", targetEntity: TEncounterEnc::class)]
    private Collection $encounters;

    public function __construct()
    {
        $this->encounters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return Collection<int, TEncounterEnc>
     */
    public function getEncounters(): Collection
    {
        return $this->encounters;
    }

    public function addEncounter(TEncounterEnc $encounter): self
    {
        if (!$this->encounters->contains($encounter)) {
            $this->encounters[] = $encounter;
            $encounter->setChampionship($this);
        }
        return $this;
    }

    public function removeEncounter(TEncounterEnc $encounter): self
    {
        if ($this->encounters->removeElement($encounter)) {
            if ($encounter->getChampionship() === $this) {
                $encounter->setChampionship(null);
            }
        }
        return $this;
    }
}
