<?php 
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: "T_USER_USR")]
class TUserUsr implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "USR_ID", type: "smallint")]
    private ?int $id = null;

    #[ORM\Column(name: "USR_TYPE", type: "string", length: 255)]
    private ?string $type = 'USER';

    #[ORM\Column(name: "USR_MAIL", type: "string", length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(name: "USR_PASS", type: "string", length: 128)]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: TTeamTem::class)]
    private Collection $teams;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
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

    public function getRoles(): array
    {
        // Retourne le rôle basé sur le type de l'utilisateur
        return [$this->type ?? 'ROLE_USER'];
    }

    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    public function eraseCredentials(): void
    {
        // Méthode requise par UserInterface, peut rester vide
    }
}
