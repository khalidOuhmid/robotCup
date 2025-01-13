<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Member entity representing a team member in the system.
 */
#[ORM\Entity]
#[ORM\Table(name: "T_MEMBER_MBR")]
class Member
{
    /**
     * @var int|null The unique identifier of the member
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "MBR_ID", type: "smallint")]
    private ?int $id = null;

    /**
     * @var string|null The first name of the member
     */
    #[ORM\Column(name: "MBR_NAME", type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le prénom est requis")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $name = null;

    /**
     * @var string|null The last name of the member
     */
    #[ORM\Column(name: "MBR_SURNAME", type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le nom est requis")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $surname = null;

    /**
     * @var string|null The email address of the member
     */
    #[ORM\Column(name: "MBR_MAIL", type: "string", length: 255, unique: true)]
    #[Assert\NotBlank(message: "L'email est requis")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide")]
    private ?string $email = null;

    /**
     * @var Team|null The team this member belongs to
     */
    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: "members")]
    #[ORM\JoinColumn(name: "TEM_ID", referencedColumnName: "TEM_ID", nullable: false)]
    private ?Team $team = null;

    /**
     * Gets the member ID.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the member's first name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Sets the member's first name.
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Gets the member's last name.
     *
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * Sets the member's last name.
     *
     * @param string $surname
     * @return self
     */
    public function setSurname(string $surname): self
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * Gets the member's email address.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets the member's email address.
     *
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Gets the team this member belongs to.
     *
     * @return Team|null
     */
    public function getTeam(): ?Team
    {
        return $this->team;
    }

    /**
     * Sets the team for this member.
     *
     * @param Team|null $team
     * @return self
     */
    public function setTeam(?Team $team): self
    {
        $this->team = $team;
        return $this;
    }

    /**
     * Returns a string representation of the member.
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s %s', $this->name ?? '', $this->surname ?? '');
    }
}
