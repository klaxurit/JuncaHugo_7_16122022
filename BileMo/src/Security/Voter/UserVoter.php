<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Company;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends Voter
{
    public const DELETE = 'USER_DELETE';
    public const VIEW = 'USER_VIEW';

    protected function supports(string $attribute, mixed $user): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::DELETE, self::VIEW])
            && $user instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, mixed $user, TokenInterface $token): bool
    {
        $company = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$company instanceof UserInterface) {
            return false;
        }

        // on vérifie si l'utilisateur (user) est lié à un client (company)
        if (null === $user->getCompany()) return false;

        return $user->getCompany() === $company;
    }
}
