<?php

namespace App\Entity;

use App\Repository\CompetitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Competition entity representing a competition in the system.
 */
#[ORM\Entity(repositoryClass: CompetitionRepository::class)]
#[ORM\Table(name: 'T_COMPETITION_CMP')]
class Competition
{
    /**
     * @var int|null The unique identifier of the competition
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'CMP_ID', type: Types::SMALLINT)]
    private ?int $id = null;

    /**
     * @var string|null The address where the competition takes place
     */
    #[ORM\Column(name: 'CMP_ADDRESS', type: 'string', length: 255, nullable: true)]
    private ?string $cmpAddress = null;

    /**
     * @var \DateTimeInterface|null The start date of the competition
     */
    #[Assert\NotNull(message: 'La date de début est requise')]
    #[ORM\Column(name: 'CMP_DATE_BEGIN', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $cmpDateBegin = null;

    /**
     * @var \DateTimeInterface|null The end date of the competition
     */
    #[Assert\NotNull(message: 'La date de fin est requise')]
    #[ORM\Column(name: 'CMP_DATE_END', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $cmpDateEnd = null;

    /**
     * @var string|null The name of the competition
     */
    #[Assert\NotNull(message: 'Le nom est requis')]
    #[Assert\Length(max: 32, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères')]
    #[ORM\Column(name: 'CMP_NAME', type: 'string', length: 32)]
    private ?string $cmpName = null;

    /**
     * @var string|null The description of the competition
     */
    #[ORM\Column(name: 'CMP_DESC', type: 'string', length: 255, nullable: true)]
    private ?string $cmpDesc = null;

    /**
     * @var Collection<int, Tournament> Collection of tournaments in this competition
     */
    #[ORM\OneToMany(mappedBy: 'competition', targetEntity: Tournament::class)]
    private Collection $tournaments;

    /**
     * @var Collection<int, Championship> Collection of championships in this competition
     */
    #[ORM\OneToMany(mappedBy: 'competition', targetEntity: Championship::class)]
    private Collection $championships;

    /**
     * @var Collection<int, Team> Collection of teams participating in this competition
     */
    #[ORM\OneToMany(mappedBy: 'competition', targetEntity: Team::class)]
    private Collection $teams;

    /**
     * Constructor initializes collections.
     */
    public function __construct()
    {
        $this->tournaments = new ArrayCollection();
        $this->championships = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    /**
     * Gets the competition ID.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the competition address.
     *
     * @return string|null
     */
    public function getCmpAddress(): ?string
    {
        return $this->cmpAddress;
    }

    /**
     * Sets the competition address.
     *
     * @param string|null $cmpAddress
     * @return self
     */
    public function setCmpAddress(?string $cmpAddress): self
    {
        $this->cmpAddress = $cmpAddress;
        return $this;
    }

    /**
     * Gets the competition start date.
     *
     * @return \DateTimeInterface|null
     */
    public function getCmpDateBegin(): ?\DateTimeInterface
    {
        return $this->cmpDateBegin;
    }

    /**
     * Sets the competition start date.
     *
     * @param \DateTimeInterface $cmpDateBegin
     * @return self
     */
    public function setCmpDateBegin(\DateTimeInterface $cmpDateBegin): self
    {
        $this->cmpDateBegin = $cmpDateBegin;
        return $this;
    }

    /**
     * Gets the competition end date.
     *
     * @return \DateTimeInterface|null
     */
    public function getCmpDateEnd(): ?\DateTimeInterface
    {
        return $this->cmpDateEnd;
    }

    /**
     * Sets the competition end date.
     *
     * @param \DateTimeInterface $cmpDateEnd
     * @return self
     */
    public function setCmpDateEnd(\DateTimeInterface $cmpDateEnd): self
    {
        $this->cmpDateEnd = $cmpDateEnd;
        return $this;
    }

    /**
     * Gets the competition name.
     *
     * @return string|null
     */
    public function getCmpName(): ?string
    {
        return $this->cmpName;
    }

    /**
     * Sets the competition name.
     *
     * @param string $cmpName
     * @return self
     */
    public function setCmpName(string $cmpName): self
    {
        $this->cmpName = $cmpName;
        return $this;
    }

    /**
     * Gets the competition description.
     *
     * @return string|null
     */
    public function getCmpDesc(): ?string
    {
        return $this->cmpDesc;
    }

    /**
     * Sets the competition description.
     *
     * @param string|null $cmpDesc
     * @return self
     */
    public function setCmpDesc(?string $cmpDesc): self
    {
        $this->cmpDesc = $cmpDesc;
        return $this;
    }

    /**
     * Gets all tournaments in this competition.
     *
     * @return Collection<int, Tournament>
     */
    public function getTournaments(): Collection
    {
        return $this->tournaments;
    }

    /**
     * Gets all championships in this competition.
     *
     * @return Collection<int, Championship>
     */
    public function getChampionships(): Collection
    {
        return $this->championships;
    }

    /**
     * Gets all teams participating in this competition.
     *
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    /**
     * Convert competition to string.
     */
    public function __toString(): string
    {
        return sprintf('%s (%s - %s)', 
            $this->cmpName, 
            $this->cmpDateBegin?->format('d/m/Y'), 
            $this->cmpDateEnd?->format('d/m/Y')
        );
    }
}
