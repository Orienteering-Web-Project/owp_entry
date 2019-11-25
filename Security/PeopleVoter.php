<?php
// src/Security/PostVoter.php
namespace Owp\OwpEntry\Security;

use Owp\OwpEntry\Entity\People;
use Owp\OwpCore\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PeopleVoter extends Voter
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

        if (!$subject instanceof People) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $people, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::DELETE:
                return $this->canDelete($people, $user);
            case self::UPDATE:
                return $this->canUpdate($people, $user);
            case self::ADD:
                return $this->canAdd($people, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canAdd(People $people, User $user)
    {
        if (!empty($people->getBase())) {
            foreach ($people->getEvent()->getPeoples() as $p) {
                if ($people->getBase() === $p->getBase()) {
                    return false;
                }
            }
        }

        return $this->security->isGranted('register', $people->getEvent());
    }

    private function canDelete(People $people, User $user)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($people->getEvent()->getDateEntries()->format('U') <= date('U')) {
            return false;
        }

        if ($people->getCreateBy() === $user || ($people->getBase() === $user->getBase() && !empty($user->getBase()))) {
            return true;
        }

        return false;
    }

    private function canUpdate(People $people, User $user)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($people->getEvent()->getDateEntries()->format('U') <= date('U')) {
            return false;
        }


        if ($people->getCreateBy() == $user || ($people->getBase === $user->getBase() && !empty($user->getBase()))) {
            return true;
        }

        return false;
    }
}
