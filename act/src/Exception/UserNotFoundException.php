<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class UserNotFoundException extends AuthenticationException
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct('', 0, null);
    }

    /**
     * @return string
     */
    public function getMessageKey(): string
    {
        return 'User not found.';
    }
}