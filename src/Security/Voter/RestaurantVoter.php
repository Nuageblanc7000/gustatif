<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class RestaurantVoter extends Voter
{

    public const VIEW = 'VIEW_PAGE_RESTO';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::VIEW])
            && $subject instanceof \App\Entity\Restaurant;

    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
  
        if (!$user instanceof UserInterface) {
            return false;
        }
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::VIEW:
                if(($subject->getUser() == $user and in_array('ROLE_RESTAURATEUR',$user->getRoles())) || in_array('ROLE_ADMIN',$user->getRoles()))
                {
                    return true;
                }else{
                    return false;
                }
                break;
        }

        return false;
    }
}
