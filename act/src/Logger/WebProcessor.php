<?php
namespace App\Logger;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class WebProcessor
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function __invoke(array $record)
    {
        $request = $this->requestStack->getCurrentRequest();
        if($request && $request->getPathInfo()=='/healthcheck'){
            unset($record['context']['method']);
            unset($record['context']['request_uri']);
            unset($record['context']['route_parameters']);
            return $record;
        }

       /* if ($request) {
            $record['extra']['host'] = $request->getHost();
            $record['extra']['url'] = $request->getRequestUri();
            $record['extra']['userIp']=$request->getClientIp();
            $record['extra']['params']=$request->getContent();
        }*/

        return $record;
    }
}
