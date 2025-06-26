<?php

namespace App\Security\Voter;

use App\Entity\Group;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupVoter extends Voter
{
    public const EDIT = 'GROUP_EDIT';
    public const DELETE = 'GROUP_DELETE';
    public const VIEW = 'GROUP_VIEW';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::VIEW])
            && $subject instanceof Group;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false; // użytkownik niezalogowany
        }

        /** @var Group $group */
        $group = $subject;

        switch ($attribute) {
            case self::VIEW:
                return true; // każdy może oglądać grupę

            case self::EDIT:
            case self::DELETE:
                return in_array('ROLE_ADMIN', $user->getRoles());

            default:
                return false;
        }
    }
}
