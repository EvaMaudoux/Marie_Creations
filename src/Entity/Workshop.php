<?php

namespace App\Entity;

use App\Repository\WorkshopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkshopRepository::class)]
class Workshop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column]
    private ?int $maxCapacity = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategoryArt $category = null;

    #[ORM\OneToMany(mappedBy: 'workshop_type', targetEntity: WorkshopSession::class)]
    private Collection $workshopSessions;

    #[ORM\Column(length: 255)]
    private ?string $image_name = null;

    public function __construct()
    {
        $this->workshopSessions = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getMaxCapacity(): ?int
    {
        return $this->maxCapacity;
    }

    public function setMaxCapacity(int $maxCapacity): self
    {
        $this->maxCapacity = $maxCapacity;

        return $this;
    }

    public function getCategory(): ?CategoryArt
    {
        return $this->category;
    }

    public function setCategory(CategoryArt $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, WorkshopSession>
     */
    public function getWorkshopSessions(): Collection
    {
        return $this->workshopSessions;
    }

    public function addWorkshopSession(WorkshopSession $workshopSession): self
    {
        if (!$this->workshopSessions->contains($workshopSession)) {
            $this->workshopSessions->add($workshopSession);
            $workshopSession->setWorkshopType($this);
        }

        return $this;
    }

    public function removeWorkshopSession(WorkshopSession $workshopSession): self
    {
        if ($this->workshopSessions->removeElement($workshopSession)) {
            // set the owning side to null (unless already changed)
            if ($workshopSession->getWorkshopType() === $this) {
                $workshopSession->setWorkshopType(null);
            }
        }

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->image_name;
    }

    public function setImageName(string $image_name): self
    {
        $this->image_name = $image_name;

        return $this;
    }
}
