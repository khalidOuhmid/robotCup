<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Team entity representing a team in the system.
 */
#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\Table(name: 'T_TEAM_TEM')]
#[UniqueEntity(
    fields: ['name', 'competition'],
    message: 'Ce nom d\'équipe est déjà utilisé dans cette compétition'
)]
class Team
{
    /**
     * @var int|null The unique identifier of the team
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'TEM_ID', type: 'smallint')]
    private ?int $id = null;

    /**
     * @var User|null The user who owns this team
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'teams')]
    #[ORM\JoinColumn(name: 'USR_ID', referencedColumnName: 'USR_ID', nullable: false)]
    private ?User $user = null;

    /**
     * @var string|null The name of the team
     */
    #[ORM\Column(name: 'TEM_NAME', type: 'string', length: 32)]
    private ?string $name = null;

    /**
     * @var string|null The structure of the team
     */
    #[ORM\Column(name: 'TEM_STRUCT', type: 'string', length: 32, nullable: true)]
    private ?string $structure = null;

    /**
     * @var \DateTimeInterface|null The creation date of the team
     */
    #[ORM\Column(name: 'TEM_CREATION_DATE', type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $creationDate;

    /**
     * @var Collection<int, Member> Collection of team members
     */
    #[ORM\OneToMany(mappedBy: 'team', targetEntity: Member::class)]
    private Collection $members;

    /**
     * @var Collection<int, Encounter> Collection of encounters where team plays as blue
     */
    #[ORM\OneToMany(mappedBy: 'teamBlue', targetEntity: Encounter::class)]
    private Collection $encountersAsBlue;

    /**
     * @var Collection<int, Encounter> Collection of encounters where team plays as green
     */
    #[ORM\OneToMany(mappedBy: 'teamGreen', targetEntity: Encounter::class)]
    private Collection $encountersAsGreen;

    /**
     * @var mixed|null Team statistics from database view
     */
    private $statistics = null;

    #[ORM\ManyToOne(targetEntity: Competition::class)]
    #[ORM\JoinColumn(name: 'CMP_ID', referencedColumnName: 'CMP_ID', nullable: false)]
    private ?Competition $competition = null;

    /**
     * Constructor initializes collections and sets creation date.
     */
    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->encountersAsBlue = new ArrayCollection();
        $this->encountersAsGreen = new ArrayCollection();
        $this->creationDate = new \DateTime(); // Set the creation date to the current date and time
    }

    /**
     * Gets the team ID.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the user who owns this team.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Sets the user who owns this team.
     *
     * @param User|null $user
     * @return self
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Gets the team name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Sets the team name.
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Gets the team structure.
     *
     * @return string|null
     */
    public function getStructure(): ?string
    {
        return $this->structure;
    }

    /**
     * Sets the team structure.
     *
     * @param string|null $structure
     * @return self
     */
    public function setStructure(?string $structure): self
    {
        $this->structure = $structure;
        return $this;
    }

    /**
     * Gets the team creation date.
     *
     * @return \DateTimeInterface|null
     */
    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    /**
     * Sets the team creation date.
     *
     * @param \DateTimeInterface $creationDate
     * @return self
     */
    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;
        return $this;
    }

    /**
     * Gets all team members.
     *
     * @return Collection<int, Member>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    /**
     * Adds a member to the team.
     *
     * @param Member $member
     * @return self
     */
    public function addMember(Member $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
            $member->setTeam($this);
        }
        return $this;
    }

    /**
     * Removes a member from the team.
     *
     * @param Member $member
     * @return self
     */
    public function removeMember(Member $member): self
    {
        if ($this->members->removeElement($member)) {
            if ($member->getTeam() === $this) {
                $member->setTeam(null);
            }
        }
        return $this;
    }

    /**
     * Gets encounters where team plays as blue.
     *
     * @return Collection<int, Encounter>
     */
    public function getEncountersAsBlue(): Collection
    {
        return $this->encountersAsBlue;
    }

    /**
     * Gets encounters where team plays as green.
     *
     * @return Collection<int, Encounter>
     */
    public function getEncountersAsGreen(): Collection
    {
        return $this->encountersAsGreen;
    }

    /**
     * Sets team statistics from database view.
     *
     * @param mixed $statistics
     * @return self
     */
    public function setStatistics($statistics): self
    {
        $this->statistics = $statistics;
        return $this;
    }

    /**
     * Gets team statistics.
     *
     * @return mixed|null
     */
    public function getStatistics()
    {
        return $this->statistics;
    }

    /**
     * Gets all championships the team participates in through encounters.
     *
     * @return array<Championship>
     */
    public function getChampionships(): array
    {
        $championships = [];
        
        foreach ($this->encountersAsBlue as $encounter) {
            if ($encounter->getChampionship() !== null) {
                $championships[] = $encounter->getChampionship();
            }
        }
        
        foreach ($this->encountersAsGreen as $encounter) {
            if ($encounter->getChampionship() !== null) {
                $championships[] = $encounter->getChampionship();
            }
        }
        
        return array_unique($championships);
    }

    /**
     * Returns a string representation of the team.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getCompetition(): ?Competition
    {
        return $this->competition;
    }

    public function setCompetition(?Competition $competition): self
    {
        $this->competition = $competition;
        return $this;
    }
}
