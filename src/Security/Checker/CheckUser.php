<?php
namespace App\Security\Checker;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

/**
 * permet de vérifier si l'email est bien confirmé avant de pouvoir rentrer sur son compte
 * configuré dans security yaml.
 */
class CheckUser implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user){
         $this->confirmIsVerified($user);
    }
    public function checkPostAuth(UserInterface $user){
         $this->confirmIsVerified($user);
    }
 public function confirmIsVerified(UserInterface $user)
 {
     if(!$user instanceof User){
          return;
     }    
     /**
      * Vérification de l'email voir si il est vérifier ou non.
      */
   if($user->isIsAcountVerified() === false){
       throw new CustomUserMessageAccountStatusException("votre email n'est pas vérifié");
   }
//    if($user->isDisabled() === true){
//      throw new CustomUserMessageAccountStatusException("Votre compte à été désactivé veuillez contactez le support.");
//    }
 }
}