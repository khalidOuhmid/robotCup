<?php

namespace App\Entity;

use App\Repository\TChampionshipChpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TChampionshipChpRepository::class)]
#[ORM\Table(name: 'T_CHAMPIONSHIP_CHP')]
class TChampionshipChp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'CHP_ID', type: Types::SMALLINT)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TCompetitionCmp::class, inversedBy: 'championships')]
    #[ORM\JoinColumn(name: 'CMP_ID', referencedColumnName: 'CMP_ID', nullable: false)]
    private ?TCompetitionCmp $competition = null;

    #[ORM\OneToMany(mappedBy: 'championship', targetEntity: TEncounterEnc::class)]
    private Collection $encounters;

    public function __construct()
    {
        $this->encounters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompetition(): ?TCompetitionCmp
    {
        return $this->competition;
    }

    public function setCompetition(?TCompetitionCmp $competition): self
    {
        $this->competition = $competition;
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
            $this->encounters->add($encounter);
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

    public function __toString(): string
    {
        return $this->getCompetition()->getCmpName() . ' - Championship ' . $this->getId();
    }
}
