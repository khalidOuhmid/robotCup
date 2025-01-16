<?php

namespace App\Entity;

use App\Repository\TournamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tournament entity representing a tournament in the system.
 */
#[ORM\Entity(repositoryClass: TournamentRepository::class)]
#[ORM\Table(name: 'T_TOURNAMENT_TNM')]
class Tournament
{
    public const TYPE_SWISS = 'SUISSE';
    public const TYPE_DUTCH = 'HOLLANDAIS';
    public const TYPE_NORMAL = 'NORMAL';

    /**
     * @var int|null The unique identifier of the tournament
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'TNM_ID', type: 'smallint')]
    private ?int $id = null;

    /**
     * @var Competition|null The competition this tournament belongs to
     */
    #[ORM\ManyToOne(targetEntity: Competition::class)]
    #[ORM\JoinColumn(name: 'CMP_ID', referencedColumnName: 'CMP_ID', nullable: false)]
    private ?Competition $competition = null;

    /**
     * @var Collection<int, Encounter> Collection of encounters in this tournament
     */
    #[ORM\OneToMany(mappedBy: 'tournament', targetEntity: Encounter::class)]
    private Collection $encounters;

    private bool $includeThirdPlace = false;


    /**
     * Constructor initializes the encounters collection.
     */
    public function __construct()
    {
        $this->encounters = new ArrayCollection();
    }

    /**
     * Gets the tournament ID.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the competition this tournament belongs to.
     *
     * @return Competition|null
     */
    public function getCompetition(): ?Competition
    {
        return $this->competition;
    }

    /**
     * Sets the competition for this tournament.
     *
     * @param Competition|null $competition
     * @return self
     */
    public function setCompetition(?Competition $competition): self
    {
        $this->competition = $competition;
        return $this;
    }

    /**
     * Gets all encounters in this tournament.
     *
     * @return Collection<int, Encounter>
     */
    public function getEncounters(): Collection
    {
        return $this->encounters;
    }

    /**
     * Adds an encounter to the tournament.
     *
     * @param Encounter $encounter
     * @return self
     */
    public function addEncounter(Encounter $encounter): self
    {
        if (!$this->encounters->contains($encounter)) {
            $this->encounters[] = $encounter;
            $encounter->setTournament($this);
        }

        return $this;
    }

    /**
     * Removes an encounter from the tournament.
     *
     * @param Encounter $encounter
     * @return self
     */
    public function removeEncounter(Encounter $encounter): self
    {
        if ($this->encounters->removeElement($encounter)) {
            // set the owning side to null (unless already changed)
            if ($encounter->getTournament() === $this) {
                $encounter->setTournament(null);
            }
        }

        return $this;
    }

    /**
     * Returns a string representation of the tournament.
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '%s - Tournament %d',
            $this->getCompetition()?->getCmpName() ?? 'Unknown Competition',
            $this->getId() ?? 0
        );
    }

    public function isIncludeThirdPlace(): bool
    {
        return $this->includeThirdPlace;
    }

    public function setIncludeThirdPlace(bool $includeThirdPlace): self
    {
        $this->includeThirdPlace = $includeThirdPlace;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->competition?->getCmpRoundSystem();
    }

    public function getName(): string
    {
        return $this->getCompetition()?->getCmpName() ?? 'Tournament without name';
    }
}
