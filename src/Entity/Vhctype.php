<?php

namespace App\Entity;

use App\Repository\VhctypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VhctypeRepository::class)]
class Vhctype
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $manufacturer = null;

    #[ORM\Column(length: 255)]
    private ?string $model = null;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Vehicle::class)]
    private Collection $vehicles;

    #[ORM\Column]
    private ?int $maxpers = null;

    #[ORM\Column]
    private ?int $palLength = null;

    #[ORM\Column]
    private ?int $palWidth = null;

    #[ORM\Column]
    private ?int $palHeight = null;

    public function __construct()
    {
        $this->vehicles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(string $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return Collection<int, Vehicle>
     */
    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function addVehicle(Vehicle $vehicle): self
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles[] = $vehicle;
            $vehicle->setType($this);
        }

        return $this;
    }

    public function removeVehicle(Vehicle $vehicle): self
    {
        if ($this->vehicles->removeElement($vehicle)) {
            // set the owning side to null (unless already changed)
            if ($vehicle->getType() === $this) {
                $vehicle->setType(null);
            }
        }

        return $this;
    }

    public function getMaxpers(): ?int
    {
        return $this->maxpers;
    }

    public function setMaxpers(int $maxpers): self
    {
        $this->maxpers = $maxpers;

        return $this;
    }

    public function getPalLength(): ?int
    {
        return $this->palLength;
    }

    public function setPalLength(int $palLength): self
    {
        $this->palLength = $palLength;

        return $this;
    }

    public function getPalWidth(): ?int
    {
        return $this->palWidth;
    }

    public function setPalWidth(int $palWidth): self
    {
        $this->palWidth = $palWidth;

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
}
