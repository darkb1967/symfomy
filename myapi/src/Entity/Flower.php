<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\FlowerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FlowerRepository::class)]
#[ApiResource(
    normalizationContext:['groups' => ['read']],
    denormalizationContext:['groups' => ['write']]
)]
class Flower
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read','write'])]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Groups(['read','write'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'flowers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read','write'])]
    private ?Category $category = null;


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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
