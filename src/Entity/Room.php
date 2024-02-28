<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RoomRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
#[ApiResource()]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity:"App\Entity\Building", inversedBy:"rooms")]
    #[ORM\JoinColumn(nullable:false)]
    private $building;

    #[ORM\OneToMany(targetEntity:"App\Entity\Station", mappedBy:"room", cascade:["remove"])]
    private $stations;

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

    public function setBuilding(?Building $building): self
    {
        $this->building = $building;

        return $this;
    }

    public function getStations(): Collection
    {
        return $this->stations;
    }
}
