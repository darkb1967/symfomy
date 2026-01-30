<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(normalizationContext:['groups' => ['get']])]
#[GET]
#[Post]

class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get'])]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Groups(['get'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Flower>
     */
    #[ORM\OneToMany(targetEntity: Flower::class, mappedBy: 'category')]
    #[Groups(['get'])]
    private Collection $flowers;

    public function __construct()
    {
        $this->flowers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Flower>
     */
    public function getFlowers(): Collection
    {
        return $this->flowers;
    }

    public function addFlower(Flower $flower): static
    {
        if (!$this->flowers->contains($flower)) {
            $this->flowers->add($flower);
            $flower->setCategory($this);
        }

        return $this;
    }

    public function removeFlower(Flower $flower): static
    {
        if ($this->flowers->removeElement($flower)) {
            // set the owning side to null (unless already changed)
            if ($flower->getCategory() === $this) {
                $flower->setCategory(null);
            }
        }

        return $this;
    }
}
