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

    #[ORM\OneToOne(inversedBy: 'tokenResolve', targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private $userCurrent;

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

    
}
