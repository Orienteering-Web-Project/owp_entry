<?php
// src/Security/PostVoter.php
namespace Owp\OwpEntry\Security;

use Owp\OwpEntry\Entity\Team;
use Owp\OwpCore\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class TeamVoter extends Voter
{
    // these strings are just invented: you can use anything
    const UPDATE = 'update';
    const DELETE = 'delete';
    const ADD    = 'add';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::DELETE, self::UPDATE, self::ADD])) {
            return false;
        }

        if (!$subject instanceof Team) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $team, TokenInterface $token)
    {
        switch ($attribute) {
            case self::DELETE:
                return $this->canDelete($team, $token);
            case self::UPDATE:
                return $this->canUpdate($team, $token);
            case self::ADD:
                return $this->canAdd($team, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canAdd(Team $team, TokenInterface $token)
    {
        return $this->security->isGranted('register', $team->getEvent());
    }

    private function canDelete(Team $team, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($team->getEvent()->getDateEntries()->format('U') <= date('U')) {
            return false;
        }

        if ($team->getCreateBy() === $user || $team->contains($user->getBase())) {
            return true;
        }

        return false;
    }

    private function canUpdate(Team $team, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($team->getEvent()->getDateEntries()->format('U') <= date('U')) {
            return false;
        }


        if ($team->getCreateBy() == $user || $team->contains($user->getBase())) {
            return true;
        }

        return false;
    }
}
