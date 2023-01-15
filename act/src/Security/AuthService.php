<?php

namespace App\Security;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;

class AuthService
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security) {

        $this->security = $security;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        $user = $this->getUserOrNull();
        if (null === $user) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    /**
     * @return User|null
     */
    public function getUserOrNull(): ?User
    {
        $user = $this->security->getUser();

        if (!($user instanceof User)) {
            return null;
        }

        return $user;
    }

    /**
     * @param bool $result
     * @return bool
     */
    public function checkRole(bool $result = false) :bool
    {
        if ($this->getUser()) {
            if ($this->getUser()->getRoles()[0] == 'ROLE_SIGNATORY' or $this->getUser()->getRoles()[0] == 'ROLE_ENTERPRISE') {
                $result = true;
            }
        }
        return $result;
    }

}