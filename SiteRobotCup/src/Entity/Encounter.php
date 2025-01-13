<?php

namespace App\Entity;

use App\Repository\EncounterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Encounter entity representing a match between two teams.
 */
#[ORM\Entity(repositoryClass: EncounterRepository::class)]
#[ORM\Table(name: 'T_ENCOUNTER_ENC')]
#[UniqueEntity(
    fields: ['teamBlue', 'teamGreen', 'dateBegin', 'dateEnd'],
    message: 'Cette rencontre existe déjà pour ces équipes et ces dates'
)]
#[UniqueEntity(
    fields: ['dateBegin', 'dateEnd', 'field'],
    message: 'Ce terrain est déjà occupé pour ces dates'
)]

class Encounter
{
    /**
     * Available states for an encounter
     */
    public const STATES = [
        'PROGRAMMEE',
        'CONCLUE',
        'EN COURS',
        'ANNULEE'
    ];

    /**
     * @var int|null The unique identifier of the encounter
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ENC_ID', type: 'smallint')]
    private ?int $id = null;

    /**
     * @var Tournament|null The tournament this encounter belongs to
     */
    #[ORM\ManyToOne(targetEntity: Tournament::class, inversedBy: 'encounters')]
    #[ORM\JoinColumn(name: 'TNM_ID', referencedColumnName: 'TNM_ID', nullable: true)]
    private ?Tournament $tournament = null;

    /**
     * @var Championship|null The championship this encounter belongs to
     */
    #[ORM\ManyToOne(targetEntity: Championship::class, inversedBy: 'encounters')]
    #[ORM\JoinColumn(name: 'CHP_ID', referencedColumnName: 'CHP_ID', nullable: true)]
    private ?Championship $championship = null;

    /**
     * @var Field|null The field where the encounter takes place
     */
    #[ORM\ManyToOne(targetEntity: Field::class, inversedBy: 'encounters')]
    #[ORM\JoinColumn(name: 'FLD_ID', referencedColumnName: 'FLD_ID', nullable: false)]
    private ?Field $field = null;

    /**
     * @var Team|null The blue team
     */
    #[Assert\NotEqualTo(
        propertyPath: "teamGreen",
        message: "Les équipes bleue et verte doivent être différentes"
    )]
    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'encountersAsBlue')]
    #[ORM\JoinColumn(name: 'TEM_ID_BLUE', referencedColumnName: 'TEM_ID', nullable: false)]
    private ?Team $teamBlue = null;

    /**
     * @var Team|null The green team
     */
    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'encountersAsGreen')]
    #[ORM\JoinColumn(name: 'TEM_ID_GREEN', referencedColumnName: 'TEM_ID', nullable: false)]
    private ?Team $teamGreen = null;

    /**
     * @var string|null The state of the encounter
     */
    #[Assert\Choice(choices: self::STATES)]
    #[ORM\Column(name: 'ENC_STATE', type: 'string', length: 255)]
    private ?string $state = null;

    /**
     * @var \DateTimeInterface|null The start date and time
     */
    #[Assert\Expression(
        "this.getState() != 'CONCLUE' or (this.getScoreBlue() !== null and this.getScoreGreen() !== null)",
        message: "Les scores doivent être définis lorsque l'état est CONCLUE"
    )]
    #[Assert\LessThan(
        propertyPath: "dateEnd",
        message: "La date de début doit être antérieure à la date de fin"
    )]
    #[ORM\Column(name: 'ENC_DATE_BEGIN', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateBegin = null;

    /**
     * @var \DateTimeInterface|null The end date and time
     */
    #[ORM\Column(name: 'ENC_DATE_END', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnd = null;

    /**
     * @var int|null The score of the blue team
     */
    #[ORM\Column(name: 'ENC_SCORE_BLUE', type: 'smallint', nullable: true)]
    private ?int $scoreBlue = null;

    /**
     * @var int|null The score of the green team
     */
    #[ORM\Column(name: 'ENC_SCORE_GREEN', type: 'smallint', nullable: true)]
    private ?int $scoreGreen = null;

    /**
     * Gets the encounter ID.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the tournament associated with this encounter.
     *
     * @return Tournament|null
     */
    public function getTournament(): ?Tournament
    {
        return $this->tournament;
    }

    /**
     * Sets the tournament for this encounter.
     *
     * @param Tournament|null $tournament
     * @return self
     */
    public function setTournament(?Tournament $tournament): self
    {
        $this->tournament = $tournament;
        return $this;
    }

    /**
     * Gets the championship associated with this encounter.
     *
     * @return Championship|null
     */
    public function getChampionship(): ?Championship
    {
        return $this->championship;
    }

    /**
     * Sets the championship for this encounter.
     *
     * @param Championship|null $championship
     * @return self
     */
    public function setChampionship(?Championship $championship): self
    {
        $this->championship = $championship;
        return $this;
    }

    /**
     * Gets the field where the encounter takes place.
     *
     * @return Field|null
     */
    public function getField(): ?Field
    {
        return $this->field;
    }

    /**
     * Sets the field for this encounter.
     *
     * @param Field|null $field
     * @return self
     */
    public function setField(?Field $field): self
    {
        $this->field = $field;
        return $this;
    }

    /**
     * Gets the blue team.
     *
     * @return Team|null
     */
    public function getTeamBlue(): ?Team
    {
        return $this->teamBlue;
    }

    /**
     * Sets the blue team.
     *
     * @param Team|null $teamBlue
     * @return self
     */
    public function setTeamBlue(?Team $teamBlue): self
    {
        $this->teamBlue = $teamBlue;
        return $this;
    }

    /**
     * Gets the green team.
     *
     * @return Team|null
     */
    public function getTeamGreen(): ?Team
    {
        return $this->teamGreen;
    }

    /**
     * Sets the green team.
     *
     * @param Team|null $teamGreen
     * @return self
     */
    public function setTeamGreen(?Team $teamGreen): self
    {
        $this->teamGreen = $teamGreen;
        return $this;
    }

    /**
     * Gets the state of the encounter.
     *
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * Sets the state of the encounter.
     *
     * @param string $state
     * @return self
     */
    public function setState(string $state): self
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Gets the start date and time of the encounter.
     *
     * @return \DateTimeInterface|null
     */
    public function getDateBegin(): ?\DateTimeInterface
    {
        return $this->dateBegin;
    }

    /**
     * Sets the start date and time of the encounter.
     *
     * @param \DateTimeInterface $dateBegin
     * @return self
     */
    public function setDateBegin(\DateTimeInterface $dateBegin): self
    {
        $this->dateBegin = $dateBegin;
        return $this;
    }

    /**
     * Gets the end date and time of the encounter.
     *
     * @return \DateTimeInterface|null
     */
    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    /**
     * Sets the end date and time of the encounter.
     *
     * @param \DateTimeInterface $dateEnd
     * @return self
     */
    public function setDateEnd(\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    /**
     * Gets the score of the blue team.
     *
     * @return int|null
     */
    public function getScoreBlue(): ?int
    {
        return $this->scoreBlue;
    }

    /**
     * Sets the score of the blue team.
     *
     * @param int|null $scoreBlue
     * @return self
     */
    public function setScoreBlue(?int $scoreBlue): self
    {
        $this->scoreBlue = $scoreBlue;
        return $this;
    }

    /**
     * Gets the score of the green team.
     *
     * @return int|null
     */
    public function getScoreGreen(): ?int
    {
        return $this->scoreGreen;
    }

    /**
     * Sets the score of the green team.
     *
     * @param int|null $scoreGreen
     * @return self
     */
    public function setScoreGreen(?int $scoreGreen): self
    {
        $this->scoreGreen = $scoreGreen;
        return $this;
    }
}
