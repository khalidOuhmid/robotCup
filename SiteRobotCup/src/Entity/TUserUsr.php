<?php 
namespace App\Entity;

use App\Repository\TUserUsrRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TUserUsrRepository::class)]
#[ORM\Table(name: "T_USER_USR")]
#[UniqueEntity(fields: ['email'], message: 'Cet email existe déjà')]
class TUserUsr implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "USR_ID", type: "smallint")]
    private ?int $id = null;

    #[Assert\Choice(choices: ['ADMIN', 'USER'])]
    #[ORM\Column(name: "USR_TYPE", type: "string", length: 255)]
    private ?string $type = 'USER';

    #[Assert\Email]
    #[ORM\Column(name: "USR_MAIL", type: "string", length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(name: "USR_PASS", type: "string", length: 128)]
    private ?string $password = null;

    #[ORM\Column(name: 'USR_CREATION_DATE', type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $creationDate;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: TTeamTem::class)]
    private Collection $teams;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->creationDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
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

    public function getRoles(): array
    {
        $roles = ['ROLE_USER']; // Tous les utilisateurs ont au moins ROLE_USER
        
        // Ajoute le rôle basé sur le type
        if ($this->type) {
            $roles[] = 'ROLE_' . strtoupper($this->type);
        }
        
        return array_unique($roles);
    }

    /**
     * @return Collection<int, TTeamTem>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(TTeamTem $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setUser($this);
        }
        return $this;
    }

    public function removeTeam(TTeamTem $team): self
    {
        if ($this->teams->removeElement($team)) {
            if ($team->getUser() === $this) {
                $team->setUser(null);
            }
        }
        return $this;
    }

    // Implémentations de UserInterface

    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    public function eraseCredentials(): void
    {
        // Méthode requise par UserInterface, peut rester vide
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
