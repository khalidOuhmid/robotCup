<?php

namespace App\Entity;

use App\Repository\TFieldFldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TFieldFldRepository::class)]
#[ORM\Table(name: 'T_FIELD_FLD')]
#[UniqueEntity(fields: ['name'], message: 'Ce nom de terrain existe déjà')]
class TFieldFld
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'FLD_ID', type: 'smallint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TCompetitionCmp::class)]
    #[ORM\JoinColumn(name: 'CMP_ID', referencedColumnName: 'CMP_ID', nullable: false)]
    private ?TCompetitionCmp $competition = null;

    #[ORM\Column(name: 'FLD_NAME', type: 'string', length: 32)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'field', targetEntity: TEncounterEnc::class)]
    private Collection $encounters;

    public function __construct()
    {
        $this->encounters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, TEncounterEnc>
     */
    public function getEncounters(): Collection
    {
        return $this->encounters;
    }
}
