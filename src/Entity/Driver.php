<?php

namespace App\Entity;

use App\Repository\DriverRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DriverRepository::class)]
class Driver
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\ManyToOne(inversedBy: 'drivers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $drivercompany = null;

    #[ORM\ManyToMany(targetEntity: Drivercat::class, inversedBy: 'drivers')]
    private Collection $drivercats;

    public function __construct()
    {
        $this->drivercats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getDrivercompany(): ?company
    {
        return $this->drivercompany;
    }

    public function setDrivercompany(?company $drivercompany): self
    {
        $this->drivercompany = $drivercompany;

        return $this;
    }

    /**
     * @return Collection<int, drivercat>
     */
    public function getDrivercats(): Collection
    {
        return $this->drivercats;
    }

    public function addDrivercat(drivercat $drivercat): self
    {
        if (!$this->drivercats->contains($drivercat)) {
            $this->drivercats[] = $drivercat;
        }

        return $this;
    }

    public function removeDrivercat(drivercat $drivercat): self
    {
        $this->drivercats->removeElement($drivercat);

        return $this;
    }
}
