<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends Voter
{
    public const DELETE = 'USER_DELETE';
    public const VIEW = 'USER_VIEW';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::DELETE, self::VIEW])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $company = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$company instanceof UserInterface) {
            return false;
        }

        $user = $subject;
        // on vérifie si l'utilisateur (user) est lié à un client (company)
        if (null === $user->getCompany()) return false;

        return $user->getCompany() === $company;
    }
}
