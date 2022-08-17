<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $code;

    #[ORM\Column(type: 'string', length: 255)]
    private $localite;

    #[ORM\Column(type: 'string', length: 255)]
    private $longitude;

    #[ORM\Column(type: 'string', length: 255)]
    private $latitude;

    #[ORM\Column(type: 'string', length: 255)]
    private $Coordonnees;

    #[ORM\Column(type: 'string', length: 255)]
    private $geom;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getLocalite(): ?string
    {
        return $this->localite;
    }

    public function setLocalite(string $localite): self
    {
        $this->localite = $localite;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getCoordonnees(): ?string
    {
        return $this->Coordonnees;
    }

    public function setCoordonnees(string $Coordonnees): self
    {
        $this->Coordonnees = $Coordonnees;

        return $this;
    }

    public function getGeom(): ?string
    {
        return $this->geom;
    }

    public function setGeom(string $geom): self
    {
        $this->geom = $geom;

        return $this;
    }
}
