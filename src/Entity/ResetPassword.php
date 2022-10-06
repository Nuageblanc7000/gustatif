<?php
namespace App\Entity;

/**
 * fausse entity pour le reset password
 */
class ResetPassword
{
    private $mail;
    

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }
}
