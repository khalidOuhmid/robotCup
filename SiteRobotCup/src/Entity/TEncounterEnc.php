<?php

namespace App\Entity;

use App\Repository\TEncounterEncRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TEncounterEncRepository::class)]
#[ORM\Table(name: 'T_ENCOUNTER_ENC')]
#[UniqueEntity(
    fields: ['teamBlue', 'teamGreen', 'dateBegin', 'dateEnd'],
    message: 'Cette rencontre existe déjà pour ces équipes et ces dates'
)]
#[UniqueEntity(
    fields: ['dateBegin', 'dateEnd', 'field'],
    message: 'Ce terrain est déjà occupé pour ces dates'
)]
class TEncounterEnc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ENC_ID', type: 'smallint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TTournamentTnm::class, inversedBy: 'encounters')]
    #[ORM\JoinColumn(name: 'TNM_ID', referencedColumnName: 'TNM_ID', nullable: true)]
    private ?TTournamentTnm $tournament = null;

    #[ORM\ManyToOne(targetEntity: TChampionshipChp::class, inversedBy: 'encounters')]
    #[ORM\JoinColumn(name: 'CHP_ID', referencedColumnName: 'CHP_ID', nullable: true)]
    private ?TChampionshipChp $championship = null;

    #[ORM\ManyToOne(targetEntity: TFieldFld::class, inversedBy: 'encounters')]
    #[ORM\JoinColumn(name: 'FLD_ID', referencedColumnName: 'FLD_ID', nullable: false)]
    private ?TFieldFld $field = null;

    #[Assert\NotEqualTo(propertyPath: "teamGreen", message: "Les équipes bleue et verte doivent être différentes")]
    #[ORM\ManyToOne(targetEntity: TTeamTem::class, inversedBy: 'encountersAsBlue')]
    #[ORM\JoinColumn(name: 'TEM_ID_BLUE', referencedColumnName: 'TEM_ID', nullable: false)]
    private ?TTeamTem $teamBlue = null;

    #[ORM\ManyToOne(targetEntity: TTeamTem::class, inversedBy: 'encountersAsGreen')]
    #[ORM\JoinColumn(name: 'TEM_ID_GREEN', referencedColumnName: 'TEM_ID', nullable: false)]
    private ?TTeamTem $teamGreen = null;

    #[Assert\Choice(choices: ['PROGRAMMEE', 'CONCLUE', 'EN COURS', 'ANNULEE'])]
    #[ORM\Column(name: 'ENC_STATE', type: 'string', length: 255)]
    private ?string $state = null;

    #[Assert\Expression(
        "this.getState() != 'CONCLUE' or (this.getScoreBlue() !== null and this.getScoreGreen() !== null)",
        message: "Les scores doivent être définis lorsque l'état est CONCLUE"
    )]
    #[Assert\LessThan(propertyPath: "dateEnd", message: "La date de début doit être antérieure à la date de fin")]
    #[ORM\Column(name: 'ENC_DATE_BEGIN', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateBegin = null;

    #[ORM\Column(name: 'ENC_DATE_END', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnd = null;

    #[ORM\Column(name: 'ENC_SCORE_BLUE', type: 'smallint', nullable: true)]
    private ?int $scoreBlue = null;

    #[ORM\Column(name: 'ENC_SCORE_GREEN', type: 'smallint', nullable: true)]
    private ?int $scoreGreen = null;

    // Getters and setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTournament(): ?TTournamentTnm
    {
        return $this->tournament;
    }

    public function setTournament(?TTournamentTnm $tournament): self
    {
        $this->tournament = $tournament;
        return $this;
    }

    public function getChampionship(): ?TChampionshipChp
    {
        return $this->championship;
    }

    public function setChampionship(?TChampionshipChp $championship): self
    {
        $this->championship = $championship;
        return $this;
    }

    public function getField(): ?TFieldFld
    {
        return $this->field;
    }

    public function setField(?TFieldFld $field): self
    {
        $this->field = $field;
        return $this;
    }

    public function getTeamBlue(): ?TTeamTem
    {
        return $this->teamBlue;
    }

    public function setTeamBlue(?TTeamTem $teamBlue): self
    {
        $this->teamBlue = $teamBlue;
        return $this;
    }

    public function getTeamGreen(): ?TTeamTem
    {
        return $this->teamGreen;
    }

    public function setTeamGreen(?TTeamTem $teamGreen): self
    {
        $this->teamGreen = $teamGreen;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;
        return $this;
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

    public function getScoreBlue(): ?int
    {
        return $this->scoreBlue;
    }

    public function setScoreBlue(?int $scoreBlue): self
    {
        $this->scoreBlue = $scoreBlue;
        return $this;
    }

    public function getScoreGreen(): ?int
    {
        return $this->scoreGreen;
    }

    public function setScoreGreen(?int $scoreGreen): self
    {
        $this->scoreGreen = $scoreGreen;
        return $this;
    }
}
