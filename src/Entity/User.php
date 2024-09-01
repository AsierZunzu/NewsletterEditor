<?php

namespace App\Entity;

use App\Enum\Roles;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, NewsletterEntry>
     */
    #[ORM\OneToMany(targetEntity: NewsletterEntry::class, mappedBy: 'createdBy', orphanRemoval: true)]
    private Collection $newsletterEntries;

    /**
     * @var Collection<int, Draft>
     */
    #[ORM\OneToMany(targetEntity: Draft::class, mappedBy: 'createdBy', orphanRemoval: true)]
    private Collection $drafts;

    public function __construct()
    {
        $this->newsletterEntries = new ArrayCollection();
        $this->drafts = new ArrayCollection();
    }

    //TODO allow disabling users

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = Roles::USER->value;

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, NewsletterEntry>
     */
    public function getNewsletterEntries(): Collection
    {
        return $this->newsletterEntries;
    }

    public function addNewsletterEntry(NewsletterEntry $newsletterEntry): static
    {
        if (!$this->newsletterEntries->contains($newsletterEntry)) {
            $this->newsletterEntries->add($newsletterEntry);
            $newsletterEntry->setCreatedBy($this);
        }

        return $this;
    }

    public function removeNewsletterEntry(NewsletterEntry $newsletterEntry): static
    {
        if ($this->newsletterEntries->removeElement($newsletterEntry)) {
            // set the owning side to null (unless already changed)
            if ($newsletterEntry->getCreatedBy() === $this) {
                $newsletterEntry->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Draft>
     */
    public function getDrafts(): Collection
    {
        return $this->drafts;
    }

    public function addDraft(Draft $draft): static
    {
        if (!$this->drafts->contains($draft)) {
            $this->drafts->add($draft);
            $draft->setCreatedBy($this);
        }

        return $this;
    }

    public function removeDraft(Draft $draft): static
    {
        if ($this->drafts->removeElement($draft)) {
            // set the owning side to null (unless already changed)
            if ($draft->getCreatedBy() === $this) {
                $draft->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->username;
    }
}
