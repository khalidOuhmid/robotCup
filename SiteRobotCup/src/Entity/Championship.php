<?php

namespace App\Entity;

use App\Repository\ChampionshipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Championship entity representing a championship in the system.
 */
#[ORM\Entity(repositoryClass: ChampionshipRepository::class)]
#[ORM\Table(name: 'T_CHAMPIONSHIP_CHP')]
class Championship
{
    /**
     * @var int|null The unique identifier of the championship
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'CHP_ID', type: Types::SMALLINT)]
    private ?int $id = null;

    /**
     * @var Competition|null The competition this championship belongs to
     */
    #[ORM\ManyToOne(targetEntity: Competition::class, inversedBy: 'championships')]
    #[ORM\JoinColumn(name: 'CMP_ID', referencedColumnName: 'CMP_ID', nullable: false)]
    private ?Competition $competition = null;

    /**
     * @var Collection<int, Encounter> Collection of encounters in this championship
     */
    #[ORM\OneToMany(mappedBy: 'championship', targetEntity: Encounter::class)]
    private Collection $encounters;

    /**
     * Constructor initializes the encounters collection.
     */
    public function __construct()
    {
        $this->encounters = new ArrayCollection();
    }

    /**
     * Gets the championship ID.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the associated competition.
     *
     * @return Competition|null
     */
    public function getCompetition(): ?Competition
    {
        return $this->competition;
    }

    /**
     * Sets the associated competition.
     *
     * @param Competition|null $competition
     * @return $this
     */
    public function setCompetition(?Competition $competition): self
    {
        $this->competition = $competition;
        return $this;
    }

    /**
     * Gets all encounters in this championship.
     *
     * @return Collection<int, Encounter>
     */
    public function getEncounters(): Collection
    {
        return $this->encounters;
    }

    /**
     * Adds an encounter to the championship.
     *
     * @param Encounter $encounter
     * @return $this
     */
    public function addEncounter(Encounter $encounter): self
    {
        if (!$this->encounters->contains($encounter)) {
            $this->encounters->add($encounter);
            $encounter->setChampionship($this);
        }
        return $this;
    }

    /**
     * Removes an encounter from the championship.
     *
     * @param Encounter $encounter
     * @return $this
     */
    public function removeEncounter(Encounter $encounter): self
    {
        if ($this->encounters->removeElement($encounter)) {
            if ($encounter->getChampionship() === $this) {
                $encounter->setChampionship(null);
            }
        }
        return $this;
    }

    /**
     * Returns a string representation of the championship.
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '%s - Championship %d',
            $this->getCompetition()->getCmpName(),
            $this->getId()
        );
    }
}
