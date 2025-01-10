<?php

namespace App\Entity;

use App\Repository\TTeamTemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TTeamTemRepository::class)]
#[ORM\Table(name: 'T_TEAM_TEM')]
#[UniqueEntity(
    fields: ['user', 'competition'],
    message: 'Cet utilisateur a déjà une équipe dans cette compétition'
)]
#[UniqueEntity(
    fields: ['name', 'competition'],
    message: 'Ce nom d\'équipe est déjà utilisé dans cette compétition'
)]
class TTeamTem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'TEM_ID', type: 'smallint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TUserUsr::class, inversedBy: 'teams')]
    #[ORM\JoinColumn(name: 'USR_ID', referencedColumnName: 'USR_ID', nullable: false)]
    private ?TUserUsr $user = null;

    #[ORM\ManyToOne(targetEntity: TCompetitionCmp::class, inversedBy: 'teams')]
    #[ORM\JoinColumn(name: 'CMP_ID', referencedColumnName: 'CMP_ID', nullable: false)]
    private ?TCompetitionCmp $competition = null;

    #[ORM\Column(name: 'TEM_NAME', type: 'string', length: 32)]
    private ?string $name = null;

    #[ORM\Column(name: 'TEM_STRUCT', type: 'string', length: 32, nullable: true)]
    private ?string $structure = null;

    #[ORM\Column(name: 'TEM_CREATION_DATE', type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $creationDate;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: TMemberMbr::class)]
    private Collection $members;

    #[ORM\OneToMany(mappedBy: 'teamBlue', targetEntity: TEncounterEnc::class)]
    private Collection $encountersAsBlue;

    #[ORM\OneToMany(mappedBy: 'teamGreen', targetEntity: TEncounterEnc::class)]
    private Collection $encountersAsGreen;

    private $statistics = null;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->encountersAsBlue = new ArrayCollection();
        $this->encountersAsGreen = new ArrayCollection();
        $this->creationDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCompetition(): ?TCompetitionCmp
    {
        return $this->competition;
    }

    public function setCompetition(?TCompetitionCmp $competition): self
    {
        $this->competition = $competition;
        return $this;
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

    public function getStructure(): ?string
    {
        return $this->structure;
    }

    public function setStructure(?string $structure): self
    {
        $this->structure = $structure;
        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;
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
            $this->members->add($member);
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

    /**
     * @return Collection<int, TEncounterEnc>
     */
    public function getEncountersAsGreen(): Collection
    {
        return $this->encountersAsGreen;
    }

    /**
     * Get team statistics from database view
     * Must be called through repository with custom query
     */
    public function setStatistics($statistics): self
    {
        $this->statistics = $statistics;
        return $this;
    }

    public function getStatistics()
    {
        return $this->statistics;
    }

    /**
     * Get championships through encounters
     * @return array<TChampionshipChp>
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

    public function __toString(): string
    {
        return $this->name;
    }
}
