<?php
/**
 * Created by PhpStorm.
 * User: Helmi
 * Date: 16/01/2020
 * Time: 15:27
 */

namespace App\Redirection;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AfterLoginRedirection implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        $roles = $token->getRoleNames();
        if ($user != null and $roles[0] == 'ROLE_USER' ) {
            return new RedirectResponse($this->router->generate('dashboard'));
        }elseif ($user != null and $roles[0] == 'ROLE_COUNSEL'){
            return new RedirectResponse($this->router->generate('dashboard_counsel'));
        }

        return new RedirectResponse($this->router->generate('welcome'));

    }

}