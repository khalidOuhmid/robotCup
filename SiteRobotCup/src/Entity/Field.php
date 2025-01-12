<?php

namespace App\Entity;

use App\Repository\FieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: FieldRepository::class)]
#[ORM\Table(name: 'T_FIELD_FLD')]
#[UniqueEntity(fields: ['name'], message: 'Ce nom de terrain existe déjà')]
class Field
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'FLD_ID', type: 'smallint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Competition::class)]
    #[ORM\JoinColumn(name: 'CMP_ID', referencedColumnName: 'CMP_ID', nullable: false)]
    private ?Competition $competition = null;

    #[ORM\Column(name: 'FLD_NAME', type: 'string', length: 32)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'field', targetEntity: Encounter::class)]
    private Collection $encounters;

    public function __construct()
    {
        $this->encounters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, Encounter>
     */
    public function getEncounters(): Collection
    {
        return $this->encounters;
    }
}
