<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: "T_TEAM_TEM")]
class TTeamTem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "TEM_ID", type: "smallint")]
    private ?int $id = null;

    #[ORM\Column(name: "TEM_NAME", type: "string", length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(name: "TEM_SCORE", type: "integer", nullable: true)]
    private ?int $score = null;

    #[ORM\ManyToOne(targetEntity: TUserUsr::class, inversedBy: "teams")]
    #[ORM\JoinColumn(name: "USR_ID", referencedColumnName: "USR_ID", nullable: false)]
    private ?TUserUsr $user = null;

    #[ORM\OneToMany(mappedBy: "team", targetEntity: TMemberMbr::class)]
    private Collection $members;

    #[ORM\OneToMany(mappedBy: "teamBlue", targetEntity: TEncounterEnc::class)]
    private Collection $encountersAsBlue;

    #[ORM\OneToMany(mappedBy: "teamGreen", targetEntity: TEncounterEnc::class)]
    private Collection $encountersAsGreen;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->encountersAsBlue = new ArrayCollection();
        $this->encountersAsGreen = new ArrayCollection();
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

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getUser(): ?TUserUsr
    {
        return $this->user;
    }

    public function setUser(?TUserUsr $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Collection<int, TMemberMbr>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(TMemberMbr $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->setTeam($this);
        }
        return $this;
    }

    public function removeMember(TMemberMbr $member): self
    {
        if ($this->members->removeElement($member)) {
            if ($member->getTeam() === $this) {
                $member->setTeam(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, TEncounterEnc>
     */
    public function getEncountersAsBlue(): Collection
    {
        return $this->encountersAsBlue;
    }

    public function addEncounterAsBlue(TEncounterEnc $encounter): self
    {
        if (!$this->encountersAsBlue->contains($encounter)) {
            $this->encountersAsBlue[] = $encounter;
            $encounter->setTeamBlue($this);
        }
        return $this;
    }

    public function removeEncounterAsBlue(TEncounterEnc $encounter): self
    {
        if ($this->encountersAsBlue->removeElement($encounter)) {
            if ($encounter->getTeamBlue() === $this) {
                $encounter->setTeamBlue(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, TEncounterEnc>
     */
    public function getEncountersAsGreen(): Collection
    {
        return $this->encountersAsGreen;
    }

    public function addEncounterAsGreen(TEncounterEnc $encounter): self
    {
        if (!$this->encountersAsGreen->contains($encounter)) {
            $this->encountersAsGreen[] = $encounter;
            $encounter->setTeamGreen($this);
        }
        return $this;
    }

    public function removeEncounterAsGreen(TEncounterEnc $encounter): self
    {
        if ($this->encountersAsGreen->removeElement($encounter)) {
            if ($encounter->getTeamGreen() === $this) {
                $encounter->setTeamGreen(null);
            }
        }
        return $this;
    }
}
