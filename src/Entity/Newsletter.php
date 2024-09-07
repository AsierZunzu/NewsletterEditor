<?php

namespace App\Entity;

use App\Repository\NewsletterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NewsletterRepository::class)]
class Newsletter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     * @var Collection<int, NewsletterEntry>
     */
    #[ORM\OneToMany(targetEntity: NewsletterEntry::class, mappedBy: 'newsletter', orphanRemoval: true)]
    private Collection $entries;

    #[ORM\Column(options: ["default" => false])]
    private ?bool $sent = false;

    public function __construct()
    {
        $this->entries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, NewsletterEntry>
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function addEntry(NewsletterEntry $entry): static
    {
        if (!$this->entries->contains($entry)) {
            $this->entries->add($entry);
            $entry->setNewsletter($this);
        }

        return $this;
    }

    public function removeEntry(NewsletterEntry $entry): static
    {
        if ($this->entries->removeElement($entry)) {
            // set the owning side to null (unless already changed)
            if ($entry->getNewsletter() === $this) {
                $entry->setNewsletter(null);
            }
        }

        return $this;
    }

    public function isSent(): ?bool
    {
        return $this->sent;
    }

    public function setSent(bool $sent): static
    {
        $this->sent = $sent;

        return $this;
    }
}
