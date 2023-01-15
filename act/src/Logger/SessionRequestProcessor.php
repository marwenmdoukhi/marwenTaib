<?php
namespace App\Logger;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SessionRequestProcessor
{
    private $session;
    private $sessionId;
    private $user;

    public function __construct(SessionInterface $session,Security $user)
    {
        $this->session = $session;
        $this->user = $user;
    }

    public function __invoke(array $record)
    {
       /* if (!$this->session->isStarted()) {
            return $record;
        }

        if (!$this->sessionId) {
            $this->sessionId = substr($this->session->getId(), 0, 8) ?: '????????';
        }

        $record['extra']['token'] = $this->sessionId.'-'.substr(uniqid('', true), -8);
        if($this->user->getUser()){
            $record['extra']['token'] = $this->sessionId.'-'.substr(uniqid('', true), -8)."-".$this->user->getUser()->getId();
        }*/


        return $record;
    }
}