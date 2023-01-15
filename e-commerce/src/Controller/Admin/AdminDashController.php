<?php

namespace App\Controller\Admin;

use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="admin_dash")
     */
    public function index(CommandeRepository $commandeRepository, UserRepository $userRepository)
    {
        $userinscri=$userRepository->userinscri();
        $benifice=$commandeRepository->benifice();
        $terminer=$commandeRepository->Terminer();
        $demande=$commandeRepository->demande();
        $userachat=$commandeRepository->meilleurvente();
        $meilleurproduit=$commandeRepository->meilleurproduit();
        return $this->render('admin/dashboard/index.html.twig', [
            'current_link' => 'dashbord',
            'userinscri'=>$userinscri,
            'benifice'=>$benifice,
            'terminer'=>$terminer,
            'demande'=>$demande,
            'userachat'=>$userachat,
            'meilleurproduit'=>$meilleurproduit,
        ]);
    }
}
