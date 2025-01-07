<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "T_MEMBER_MBR")]
class TMemberMbr
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "MBR_ID", type: "smallint")]
    private ?int $id = null;

    #[ORM\Column(name: "MBR_NAME", type: "string", length: 255)]
    private ?string $name = null;

    #[ORM\Column(name: "MBR_SURNAME", type: "string", length: 255)]
    private ?string $surname = null;

    #[ORM\Column(name: "MBR_MAIL", type: "string", length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: TTeamTem::class, inversedBy: "members")]
    #[ORM\JoinColumn(name: "TEM_ID", referencedColumnName: "TEM_ID", nullable: false)]
    private ?TTeamTem $team = null;

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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getTeam(): ?TTeamTem
    {
        return $this->team;
    }

    public function setTeam(?TTeamTem $team): self
    {
        $this->team = $team;
        return $this;
    }
}
