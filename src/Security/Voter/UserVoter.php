<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';
    public const DELETE = 'USER_DELETE';
    public CONST VIEW_RESTAURATEUR = 'RESTO_VIEW';
    public CONST VIEW_USER = 'USER_VIEW';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html

        return in_array($attribute, [self::EDIT, self::DELETE , self::VIEW_USER  , self::VIEW_RESTAURATEUR ])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                if (!$token->getUser()) {
                    return false;
                }
                break;
                case self::VIEW_RESTAURATEUR:
                    if (in_array('ROLE_RESTAURATEUR',$token->getUser()->getRoles())) {
                        return true;
                    }
                    break;
                case self::VIEW_USER:
                    if (in_array('ROLE_SIMPLE_USER',$token->getUser()->getRoles())) {      
                        return true;
                    }
                    break;
            case self::DELETE:
                if (!$user instanceof UserInterface && !$token->getUser()) {
                    dd($token->getUser());
                    return false;
                }
                break;
        }

        return true;
    }
}
