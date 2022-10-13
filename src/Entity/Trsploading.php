<?php

namespace App\Entity;

use App\Repository\TrsploadingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrsploadingRepository::class)]
class Trsploading
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne]
    private ?Vehicle $vhc = null;

    #[ORM\ManyToOne(inversedBy: 'trsploadings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Transport $transport = null;

    #[ORM\Column]
    private ?int $palHeight = null;

    #[ORM\Column(nullable: true)]
    private ?int $coord_x = null;

    #[ORM\Column(nullable: true)]
    private ?int $coord_y = null;

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

    public function getVhc(): ?Vehicle
    {
        return $this->vhc;
    }

    public function setVhc(?Vehicle $vhc): self
    {
        $this->vhc = $vhc;

        return $this;
    }

    public function getTransport(): ?transport
    {
        return $this->transport;
    }

    public function setTransport(?transport $transport): self
    {
        $this->transport = $transport;

        return $this;
    }

    public function getPalHeight(): ?int
    {
        return $this->palHeight;
    }

    public function setPalHeight(int $palHeight): self
    {
        $this->palHeight = $palHeight;

        return $this;
    }

    public function getCoordX(): ?int
    {
        return $this->coord_x;
    }

    public function setCoordX(?int $coord_x): self
    {
        $this->coord_x = $coord_x;

        return $this;
    }

    public function getCoordY(): ?int
    {
        return $this->coord_y;
    }

    public function setCoordY(?int $coord_y): self
    {
        $this->coord_y = $coord_y;

        return $this;
    }
}
