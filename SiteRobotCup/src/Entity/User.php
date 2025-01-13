<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User entity representing a user in the system.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "T_USER_USR")]
#[UniqueEntity(fields: ['email'], message: 'Cet email existe déjà')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Available user types
     */
    public const USER_TYPES = ['ADMIN', 'USER'];
    public const DEFAULT_TYPE = 'USER';

    /**
     * @var int|null The unique identifier of the user
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "USR_ID", type: "smallint")]
    private ?int $id = null;

    /**
     * @var string|null The type of user (ADMIN or USER)
     */
    #[Assert\Choice(choices: self::USER_TYPES, message: 'Type utilisateur invalide')]
    #[ORM\Column(name: "USR_TYPE", type: "string", length: 255)]
    private ?string $type = self::DEFAULT_TYPE;

    /**
     * @var string|null The email address of the user
     */
    #[Assert\Email(message: 'L\'adresse email {{ value }} n\'est pas valide')]
    #[Assert\NotBlank(message: 'L\'email est requis')]
    #[ORM\Column(name: "USR_MAIL", type: "string", length: 255, unique: true)]
    private ?string $email = null;

    /**
     * @var string|null The hashed password
     */
    #[Assert\NotBlank(message: 'Le mot de passe est requis')]
    #[ORM\Column(name: "USR_PASS", type: "string", length: 128)]
    private ?string $password = null;

    /**
     * @var \DateTimeInterface|null The creation date of the user account
     */
    #[ORM\Column(name: 'USR_CREATION_DATE', type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $creationDate;

    /**
     * @var Collection<int, Team> Collection of teams owned by the user
     */
    #[ORM\OneToMany(mappedBy: "user", targetEntity: Team::class)]
    private Collection $teams;

    /**
     * Constructor initializes collections and sets default values.
     */
    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->creationDate = new \DateTime();
    }

    /**
     * Gets the user ID.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the user type.
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Sets the user type.
     *
     * @param string $type
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Gets the user's email address.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets the user's email address.
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
     * Gets the hashed password.
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Sets the hashed password.
     *
     * @param string $password
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Gets the account creation date.
     *
     * @return \DateTimeInterface|null
     */
    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    /**
     * Sets the account creation date.
     *
     * @param \DateTimeInterface $creationDate
     * @return self
     */
    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;
        return $this;
    }

    /**
     * Gets the user's roles.
     *
     * @return array<string>
     */
    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        if ($this->type) {
            $roles[] = 'ROLE_' . strtoupper($this->type);
        }
        return array_unique($roles);
    }

    /**
     * Gets all teams owned by the user.
     *
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    /**
     * Adds a team to the user's teams.
     *
     * @param Team $team
     * @return self
     */
    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setUser($this);
        }
        return $this;
    }

    /**
     * Removes a team from the user's teams.
     *
     * @param Team $team
     * @return self
     */
    public function removeTeam(Team $team): self
    {
        if ($this->teams->removeElement($team)) {
            if ($team->getUser() === $this) {
                $team->setUser(null);
            }
        }
        return $this;
    }

    /**
     * Gets the user identifier (email).
     *
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    /**
     * Required by UserInterface, can be empty.
     */
    public function eraseCredentials(): void
    {
        // Method intentionally left empty
    }

    /**
     * Returns a string representation of the user.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->email ?? '';
    }
}
