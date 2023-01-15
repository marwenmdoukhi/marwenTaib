<?php

namespace App\Controller\Client;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class loginAction extends AbstractController
{
    /**
     * @Route("/login/header", name="account_login_header")
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function __invoke(AuthenticationUtils $utils)
    {
        $authError = $utils->getLastAuthenticationError();
        if ($authError !== null) {
            return new Response(0);
        }
        return $this->render('client/account/loginheader.html.twig', [
            'authError' => $authError !== null,
        ]);
    }
}
