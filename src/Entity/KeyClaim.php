<?php

namespace App\Entity;

use App\Repository\KeyClaimRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KeyClaimRepository::class)]
class KeyClaim
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicle $vehicle = null;

    #[ORM\Column(length: 255)]
    private ?string $Person = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $claimingDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $ReceptionDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getPerson(): ?string
    {
        return $this->Person;
    }

    public function setPerson(string $Person): self
    {
        $this->Person = $Person;

        return $this;
    }

    public function getClaimingDate(): ?\DateTimeInterface
    {
        return $this->claimingDate;
    }

    public function setClaimingDate(\DateTimeInterface $claimingDate): self
    {
        $this->claimingDate = $claimingDate;

        return $this;
    }

    public function getReceptionDate(): ?\DateTimeInterface
    {
        return $this->ReceptionDate;
    }

    public function setReceptionDate(?\DateTimeInterface $ReceptionDate): self
    {
        $this->ReceptionDate = $ReceptionDate;

        return $this;
    }
}
