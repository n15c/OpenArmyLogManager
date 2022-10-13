<?php

namespace App\Entity;

use App\Repository\TransportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TransportRepository::class)]
class Transport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $trspuuid = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $trspdate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $unit = null;

    #[ORM\OneToMany(mappedBy: 'transport', targetEntity: Trsploading::class, orphanRemoval: true)]
    private Collection $trsploadings;

    #[ORM\Column]
    private ?bool $completed = null;

    #[ORM\Column(length: 255)]
    private ?string $alias = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\ManyToMany(targetEntity: Vehicle::class, inversedBy: 'transports')]
    private Collection $vehicles;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    public function __construct()
    {
        $this->vhc = new ArrayCollection();
        $this->trsploadings = new ArrayCollection();
        $this->setTrspuuid(Uuid::V4());
        $this->vehicles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrspuuid(): ?string
    {
        return $this->trspuuid;
    }

    public function setTrspuuid(string $trspuuid): self
    {
        $this->trspuuid = $trspuuid;

        return $this;
    }

    public function getTrspdate(): ?\DateTimeInterface
    {
        return $this->trspdate;
    }

    public function setTrspdate(\DateTimeInterface $trspdate): self
    {
        $this->trspdate = $trspdate;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * @return Collection<int, Trsploading>
     */
    public function getTrsploadings(): Collection
    {
        return $this->trsploadings;
    }

    public function addTrsploading(Trsploading $trsploading): self
    {
        if (!$this->trsploadings->contains($trsploading)) {
            $this->trsploadings[] = $trsploading;
            $trsploading->setTransport($this);
        }

        return $this;
    }

    public function removeTrsploading(Trsploading $trsploading): self
    {
        if ($this->trsploadings->removeElement($trsploading)) {
            // set the owning side to null (unless already changed)
            if ($trsploading->getTransport() === $this) {
                $trsploading->setTransport(null);
            }
        }

        return $this;
    }

    public function isCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection<int, vehicle>
     */
    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function addVehicle(Vehicle $vehicle): self
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles[] = $vehicle;
        }

        return $this;
    }

    public function removeVehicle(Vehicle $vehicle): self
    {
        $this->vehicles->removeElement($vehicle);

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }
}
