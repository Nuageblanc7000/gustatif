<?php

namespace App\Entity;

use App\Repository\TokenResolveRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TokenResolveRepository::class)]
class TokenResolve
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $token;


    private $user;

    #[ORM\OneToOne( inversedBy: 'tokenResolve', targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $userCurrent;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();       
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getUserCurrent(): ?User
    {
        return $this->userCurrent;
    }

    public function setUserCurrent(User $userCurrent): self
    {
        $this->userCurrent = $userCurrent;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    
}
