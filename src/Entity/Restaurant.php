<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
#[UniqueEntity('name',message:'Ce nom est déjà utilisé')]
class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(max:40,maxMessage:'Le nom utilisé est trop long')]
    #[Assert\NotBlank(message:'Veuillez indiqer une valeur')]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\Length(max:750,maxMessage:'Votre description dépasse le nombre de caractères autorisé')]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Veuillez indiqer une valeur')]
    private ?string $adress = null;


    #[Assert\Regex(pattern:'/^\s?(\d{4})\s?(\d{2})\s?(\d{2})\s?(\d{2})\s?$/i',message:'veuillez insérer un numéro valide')]
    #[Assert\NotBlank(message:'Veuillez indiqer une valeur')]
    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\ManyToOne(inversedBy: 'restaurants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[ORM\OneToMany(mappedBy: 'restaurant', targetEntity: Plat::class)]
    private Collection $plat;

    #[Assert\Valid]
    #[Assert\All(new NotBlank(message:'Veuillez indiqer une valeur'))]
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'restaurants')]
    private Collection $category;
    
    
    #[Assert\Valid]
    #[Assert\All(new NotBlank(message:'Veuillez indiqer une valeur'))]
    #[ORM\ManyToMany(targetEntity: Origine::class, inversedBy: 'restaurants')]
    private Collection $origine;

    #[ORM\OneToMany(mappedBy: 'restaurant', targetEntity: Image::class, orphanRemoval: true)]
    private Collection $images;

    #[ORM\Column(length: 255)]
    private ?string $cover = null;

    public function __construct()
    {
        $this->plat = new ArrayCollection();
        $this->category = new ArrayCollection();
        $this->origine = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection<int, Plat>
     */
    public function getPlat(): Collection
    {
        return $this->plat;
    }

    public function addPlat(Plat $plat): self
    {
        if (!$this->plat->contains($plat)) {
            $this->plat->add($plat);
            $plat->setRestaurant($this);
        }

        return $this;
    }

    public function removePlat(Plat $plat): self
    {
        if ($this->plat->removeElement($plat)) {
            // set the owning side to null (unless already changed)
            if ($plat->getRestaurant() === $this) {
                $plat->setRestaurant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->category->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Origine>
     */
    public function getOrigine(): Collection
    {
        return $this->origine;
    }

    public function addOrigine(Origine $origine): self
    {
        if (!$this->origine->contains($origine)) {
            $this->origine->add($origine);
        }

        return $this;
    }

    public function removeOrigine(Origine $origine): self
    {
        $this->origine->removeElement($origine);

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setRestaurant($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getRestaurant() === $this) {
                $image->setRestaurant(null);
            }
        }

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }
}
