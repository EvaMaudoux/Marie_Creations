<?php

namespace App\Entity;

use App\Repository\CategoryArtRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryArtRepository::class)]
class CategoryArt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Creation::class)]
    private Collection $creations;

    public function __construct()
    {
        $this->creations = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Creation>
     */
    public function getCreations(): Collection
    {
        return $this->creations;
    }

    public function addCreation(Creation $creation): self
    {
        if (!$this->creations->contains($creation)) {
            $this->creations->add($creation);
            $creation->setCategory($this);
        }

        return $this;
    }

    public function removeCreation(Creation $creation): self
    {
        if ($this->creations->removeElement($creation)) {
            // set the owning side to null (unless already changed)
            if ($creation->getCategory() === $this) {
                $creation->setCategory(null);
            }
        }

        return $this;
    }

    // EAsyAdmin - classes relationnelles
    public function __toString(): string
    {
        return $this->name;
    }
}
