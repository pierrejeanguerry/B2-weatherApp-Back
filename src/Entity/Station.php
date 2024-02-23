<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\StationRepository;
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

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable:true)]
    private ?\DateTimeInterface $activation_date = null;

    #[ORM\ManyToOne(targetEntity:"App\Entity\Room", inversedBy:"stations")]
    #[ORM\JoinColumn(nullable:true)]
    private $room;

    #[ORM\Column(length: 64)]
    private ?string $token = null;

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

    public function getActivationDate(): ?\DateTimeInterface
    {
        return $this->activation_date;
    }

    public function setActivationDate(\DateTimeInterface $activation_date): static
    {
        $this->activation_date = $activation_date;

        return $this;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }
}
