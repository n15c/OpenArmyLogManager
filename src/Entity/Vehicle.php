<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 6)]
    private ?string $licplate = null;

    #[ORM\ManyToOne(inversedBy: 'vehicles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vhctype $type = null;

    #[ORM\ManyToOne(inversedBy: 'vehicles')]
    private ?Company $company = null;

    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: VehicleRemark::class, orphanRemoval: true)]
    private Collection $vehicleRemarks;

    #[ORM\ManyToMany(targetEntity: Order::class, mappedBy: 'vehicles')]
    private Collection $orders;

    #[ORM\Column]
    private ?bool $operational = true;

    #[ORM\ManyToMany(targetEntity: Transport::class, mappedBy: 'vehicles')]
    private Collection $transports;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->vehicleRemarks = new ArrayCollection();
        $this->transports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLicplate(): ?string
    {
        return $this->licplate;
    }

    public function setLicplate(string $licplate): self
    {
        $this->licplate = $licplate;

        return $this;
    }

    public function getType(): ?Vhctype
    {
        return $this->type;
    }

    public function setType(?Vhctype $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection<int, VehicleRemark>
     */
    public function getVehicleRemarks(): Collection
    {
        return $this->vehicleRemarks;
    }

    public function addVehicleRemark(VehicleRemark $vehicleRemark): self
    {
        if (!$this->vehicleRemarks->contains($vehicleRemark)) {
            $this->vehicleRemarks[] = $vehicleRemark;
            $vehicleRemark->setVehicle($this);
        }

        return $this;
    }

    public function removeVehicleRemark(VehicleRemark $vehicleRemark): self
    {
        if ($this->vehicleRemarks->removeElement($vehicleRemark)) {
            // set the owning side to null (unless already changed)
            if ($vehicleRemark->getVehicle() === $this) {
                $vehicleRemark->setVehicle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->addVehicle($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            $order->removeVehicle($this);
        }

        return $this;
    }

    public function isOperational(): ?bool
    {
        return $this->operational;
    }

    public function setOperational(bool $operational): self
    {
        $this->operational = $operational;

        return $this;
    }

    /**
     * @return Collection<int, Transport>
     */
    public function getTransports(): Collection
    {
        return $this->transports;
    }

    public function addTransport(Transport $transport): self
    {
        if (!$this->transports->contains($transport)) {
            $this->transports[] = $transport;
            $transport->addVehicle($this);
        }

        return $this;
    }

    public function removeTransport(Transport $transport): self
    {
        if ($this->transports->removeElement($transport)) {
            $transport->removeVehicle($this);
        }

        return $this;
    }
}
