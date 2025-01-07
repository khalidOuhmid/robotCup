<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "T_ENCOUNTER_ENC")]
class TEncounterEnc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "ENC_ID", type: "smallint")]
    private ?int $id = null;

    #[ORM\Column(name: "ENC_SCORE_BLUE", type: "integer", nullable: true)]
    private ?int $scoreBlue = null;

    #[ORM\Column(name: "ENC_SCORE_GREEN", type: "integer", nullable: true)]
    private ?int $scoreGreen = null;

    #[ORM\Column(name: "ENC_STATE", type: "string", length: 255)]
    private ?string $state = null;

    #[ORM\ManyToOne(targetEntity: TTeamTem::class, inversedBy: "encountersAsBlue")]
    #[ORM\JoinColumn(name: "TEM_ID_PARTICIPATE_BLUE", referencedColumnName: "TEM_ID", nullable: false)]
    private ?TTeamTem $teamBlue = null;

    #[ORM\ManyToOne(targetEntity: TTeamTem::class, inversedBy: "encountersAsGreen")]
    #[ORM\JoinColumn(name: "TEM_ID_PARTICIPATE_GREEN", referencedColumnName: "TEM_ID", nullable: false)]
    private ?TTeamTem $teamGreen = null;

    #[ORM\ManyToOne(targetEntity: TChampionshipChp::class, inversedBy: "encounters")]
    #[ORM\JoinColumn(name: "CHP_ID", referencedColumnName: "CHP_ID", nullable: false)]
    private ?TChampionshipChp $championship = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScoreBlue(): ?int
    {
        return $this->scoreBlue;
    }

    public function setScoreBlue(?int $scoreBlue): self
    {
        $this->scoreBlue = $scoreBlue;
        if ($this->teamBlue) {
            $this->teamBlue->updateScore();
        }
        return $this;
    }

    
public function setScoreGreen(?int $scoreGreen): self
{
    $this->scoreGreen = $scoreGreen;
    if ($this->teamGreen) {
        $this->teamGreen->updateScore();
    }
    return $this;
}
public function getScoreGreen(): ?int
{
    return $this->scoreGreen;
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

    public function getChampionship(): ?TChampionshipChp
    {
        return $this->championship;
    }

    public function setChampionship(?TChampionshipChp $championship): self
    {
        $this->championship = $championship;
        return $this;
    }
}
