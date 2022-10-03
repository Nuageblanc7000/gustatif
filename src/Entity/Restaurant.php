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
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
#[UniqueEntity(fields:'name')]
class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(max:40)]
    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\Length(min:30,max:750)]
    #[Assert\NotBlank()]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $adress = null;

    #[Assert\Length(max:16)]
    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\ManyToOne(inversedBy: 'restaurants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[Assert\Valid]
    #[Assert\All(new NotBlank())]
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'restaurants')]
    private Collection $category;
    
    
    #[Assert\Valid]
    #[Assert\All(new NotNull())]
    #[ORM\ManyToMany(targetEntity: Origine::class, inversedBy: 'restaurants')]
    private Collection $origine;

    #[ORM\OneToMany(mappedBy: 'restaurant', targetEntity: Image::class, orphanRemoval: true)]
    private Collection $images;

    #[ORM\Column(length: 255)]
    private ?string $cover = null;


    #[ORM\ManyToOne(inversedBy: 'restaurant')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'restaurant', targetEntity: Plat::class, orphanRemoval:true)]
    private Collection $plats;

    #[ORM\OneToOne(inversedBy: 'restaurant', cascade: ['persist', 'remove'])]
    private ?Schedule $schedule = null;

    #[ORM\OneToMany(mappedBy: 'resto', targetEntity: Comment::class, orphanRemoval:true)]
    private Collection $comments;

    public int $ratio;

    #[ORM\OneToMany(mappedBy: 'restaurant', targetEntity: Like::class, orphanRemoval: true)]
    private Collection $likes;

    public function __construct()
    {
        $this->plat = new ArrayCollection();
        $this->category = new ArrayCollection();
        $this->origine = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->plats = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Plat>
     */
    public function getPlats(): Collection
    {
        return $this->plats;
    }

    public function addPlat(Plat $plat): self
    {
        if (!$this->plats->contains($plat)) {
            $this->plats->add($plat);
            $plat->setRestaurant($this);
        }

        return $this;
    }

    public function removePlat(Plat $plat): self
    {
        if ($this->plats->removeElement($plat)) {
            // set the owning side to null (unless already changed)
            if ($plat->getRestaurant() === $this) {
                $plat->setRestaurant(null);
            }
        }

        return $this;
    }

    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    public function setSchedule(?Schedule $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setResto($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getResto() === $this) {
                $comment->setResto(null);
            }
        }

        return $this;
    }


    //function rating

    public function getNote(): int
    {
        $calc = 0;
        foreach ($this->getComments() as  $rating) {
           $note =  $rating->getRating();
            $calc += $note;
        }
        $count = count($this->getComments());
        $reponse = $this->ratio = $calc === 0 ? 0 : $this->ratio = round($calc / $count);
        return $this->ratio = $reponse;
    }
   

    /**
     * Set the value of ratio
     *
     * @return  self
     */ 
    public function setRatio(int $ratio)
    {
        $this->ratio = $ratio;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setRestaurant($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getRestaurant() === $this) {
                $like->setRestaurant(null);
            }
        }

        return $this;
    }

    /**
     * on regarde si le user en cours aime déjà le restaurant
     *
     * @param User $user
     * @return boolean
     */
    public function isLikeByUser(User $user) : bool  
    {
        foreach ($this->likes as  $like) {
            if($like->getUser() === $user)
            {
                return true;
            }
        }
        return false;
    }


    public function isUserCommented(User $user) : bool  
    {
        foreach ($this->comments as  $comment) {
            if($comment->getAuthor() === $user)
            {
                return true;
            }
        }
        return false;
    }
}
