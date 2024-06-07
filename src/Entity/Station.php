<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\StationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StationRepository::class)]
#[ApiResource()]
class Station
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable:false, type: Types::INTEGER)] //0 => eteint/deco, 1 => fonctionnel/connecté, 2 => defectueux
    private ?int $state = 0;

    #[ORM\Column(nullable:true, length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable:true, type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $activation_date = null;

    #[ORM\ManyToOne(targetEntity:"App\Entity\Building", inversedBy:"stations")]
    #[ORM\JoinColumn(nullable:true)]
    private $building;

    #[ORM\OneToMany(targetEntity:"App\Entity\Reading", mappedBy:"station", cascade:["remove"])]
    private $readings;

    #[ORM\Column(length: 17, unique:true)]
    private ?string $mac = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getActivationDate(): ?\DateTimeInterface
    {
        return $this->activation_date;
    }

    public function setActivationDate(?\DateTimeInterface $activation_date): static
    {
        $this->activation_date = $activation_date;

        return $this;
    }

    public function setBuilding(?Building $building): self
    {
        $this->building = $building;
        return $this;
    }

    public function getMac(): ?string
    {
        return $this->mac;
    }

    public function setMac(string $mac): static
    {
        $this->mac = $mac;

        return $this;
    }

    public function getReadings(): Collection
    {
        return $this->readings;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(?int $state): static
    {
        $this->state = $state;

        return $this;
    }
}
