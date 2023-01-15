<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserInscriController extends AbstractController
{
    /**
     * @Route("/admin/user", name="user_inscri")
     */
    public function index(UserRepository  $repository): Response
    {
        return $this->render('admin/userinscri/index.html.twig', [
            'user'=>$repository->findAll(),
            'current_link' => 'user',
        ]);
    }
}
