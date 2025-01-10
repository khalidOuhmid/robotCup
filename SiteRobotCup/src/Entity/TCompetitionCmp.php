<?php

namespace App\Entity;

use App\Repository\TCompetitionCmpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TCompetitionCmpRepository::class)]
#[ORM\Table(name: 'T_COMPETITION_CMP')]
class TCompetitionCmp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'CMP_ID', type: Types::SMALLINT)]
    private ?int $id = null;

    #[ORM\Column(name: 'CMP_ADDRESS', type: 'string', length: 255, nullable: true)]
    private ?string $cmpAddress = null;

    #[Assert\NotNull]
    #[ORM\Column(name: 'CMP_DATE_BEGIN', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $cmpDateBegin = null;

    #[Assert\NotNull]
    #[ORM\Column(name: 'CMP_DATE_END', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $cmpDateEnd = null;

    #[Assert\NotNull]
    #[Assert\Length(max: 32)]
    #[ORM\Column(name: 'CMP_NAME', type: 'string', length: 32)]
    private ?string $cmpName = null;

    #[ORM\Column(name: 'CMP_DESC', type: 'string', length: 255, nullable: true)]
    private ?string $cmpDesc = null;

    #[ORM\OneToMany(mappedBy: 'competition', targetEntity: TTournamentTnm::class)]
    private Collection $tournaments;

    #[ORM\OneToMany(mappedBy: 'competition', targetEntity: TChampionshipChp::class)]
    private Collection $championships;

    #[ORM\OneToMany(mappedBy: 'competition', targetEntity: TTeamTem::class)]
    private Collection $teams;

    public function __construct()
    {
        $this->tournaments = new ArrayCollection();
        $this->championships = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCmpAddress(): ?string
    {
        return $this->cmpAddress;
    }

    public function setCmpAddress(?string $cmpAddress): self
    {
        $this->cmpAddress = $cmpAddress;
        return $this;
    }

    public function getCmpDateBegin(): ?\DateTimeInterface
    {
        return $this->cmpDateBegin;
    }

    public function setCmpDateBegin(\DateTimeInterface $cmpDateBegin): self
    {
        $this->cmpDateBegin = $cmpDateBegin;
        return $this;
    }

    public function getCmpDateEnd(): ?\DateTimeInterface
    {
        return $this->cmpDateEnd;
    }

    public function setCmpDateEnd(\DateTimeInterface $cmpDateEnd): self
    {
        $this->cmpDateEnd = $cmpDateEnd;
        return $this;
    }

    public function getCmpName(): ?string
    {
        return $this->cmpName;
    }

    public function setCmpName(string $cmpName): self
    {
        $this->cmpName = $cmpName;
        return $this;
    }

    public function getCmpDesc(): ?string
    {
        return $this->cmpDesc;
    }

    public function setCmpDesc(?string $cmpDesc): self
    {
        $this->cmpDesc = $cmpDesc;
        return $this;
    }

    /**
     * @return Collection<int, TTournamentTnm>
     */
    public function getTournaments(): Collection
    {
        return $this->tournaments;
    }

    /**
     * @return Collection<int, TChampionshipChp>
     */
    public function getChampionships(): Collection
    {
        return $this->championships;
    }

    /**
     * @return Collection<int, TTeamTem>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function __toString(): string
    {
        return $this->getCmpName() ?: '';
    }
}
