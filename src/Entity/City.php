<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[Groups('city-read')]
    #[ORM\Column(type: 'integer')]
    private $code;
    #[Groups('city-read')]
    #[ORM\Column(type: 'string', length: 255)]
    private $localite;
    #[Groups('city-read')]
    #[ORM\Column(type: 'string', length: 255)]
    private $longitude;
    #[Groups('city-read')]
    #[ORM\Column(type: 'string', length: 255)]
    private $latitude;
    #[Groups('city-read')]
    #[ORM\Column(type: 'string', length: 255)]
    private $Coordonnees;
    #[Groups('city-read')]
    #[ORM\Column(type: 'string', length: 255)]
    private $geom;

    #[ORM\OneToMany(mappedBy: 'city', targetEntity: User::class)]
    private $user;

    #[ORM\OneToMany(mappedBy: 'city', targetEntity: Restaurant::class)]
    private Collection $restaurants;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->restaurants = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
            $user->setCity($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCity() === $this) {
                $user->setCity(null);
            }
        }

        return $this;
    }

    function __toString() : string
    {
        return $this->localite;
    }

    /**
     * @return Collection<int, Restaurant>
     */
    public function getRestaurants(): Collection
    {
        return $this->restaurants;
    }

    public function addRestaurant(Restaurant $restaurant): self
    {
        if (!$this->restaurants->contains($restaurant)) {
            $this->restaurants->add($restaurant);
            $restaurant->setCity($this);
        }

        return $this;
    }

    public function removeRestaurant(Restaurant $restaurant): self
    {
        if ($this->restaurants->removeElement($restaurant)) {
            // set the owning side to null (unless already changed)
            if ($restaurant->getCity() === $this) {
                $restaurant->setCity(null);
            }
        }

        return $this;
    }
}
