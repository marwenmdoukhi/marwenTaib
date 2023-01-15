<?php

namespace App\Controller\Client;

use App\Entity\PasswordUpdate;
use App\Form\AccountEditType;
use App\Form\PasswordUpdateType;
use App\Repository\CommandeArticleRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfilController extends AbstractController
{
    /**
     * @Route("/mon-compte", name="account_profil")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        if (!$this->getUser()) {
            return  $this->redirectToRoute('account_login');
        }
        return $this->render('client/profil/index.html.twig', [
        ]);
    }

    /**
     * @Route("/modifier_mon_profil", name="editerprofil")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function profile(Request $request, EntityManagerInterface $manager)
    {
        if (!$this->getUser()) {
            return  $this->redirectToRoute('account_login');
        }
        $user = $this->getUser();
        $form = $this->createForm(AccountEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre information a bien été modifié"
            );

            return  $this->redirectToRoute('editerprofil');
        }
        return $this->render('client/profil/modifier.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifier-mot-de-passe", name="user_password")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        if (!$this->getUser()) {
            return  $this->redirectToRoute('account_login');
        }
        $updatePassword = new PasswordUpdate();
        $user = $this->getUser();
        $form = $this->createForm(PasswordUpdateType::class, $updatePassword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $updatePassword->getNewPassword();
            $hash = $encoder->encodePassword($user, $newPassword);
            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre mot de passe a bien été modifié"
            );

            return $this->redirectToRoute('user_password');
        }

        return $this->render('client/profil/password.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/mes-commandes", name="mescommande")
     * @return Response
     */
    public function mescommande(CommandeRepository  $commandeRepository)
    {
        if (!$this->getUser()) {
            return  $this->redirectToRoute('account_login');
        }
        $mescommande =$commandeRepository->mescommande($this->getUser());
        return $this->render(
            'client/profil/mescommande.html.twig',
            [
                'mescommande' => $mescommande
            ]
        );
    }

    /**
     * @Route("/mon-compte/detailcomnnde/{id}", name="detailcomnnde")
     */
    public function detailcomnnde($id, CommandeArticleRepository  $repository)
    {
        if (!$this->getUser()) {
            return  $this->redirectToRoute('account_login');
        }
        $detailcommande=$repository->detailcommande($id, $this->getUser());
        return $this->render(
            'client/profil/detailcommande.html.twig',
            [
                'detailcommande' => $detailcommande,
            ]
        );
    }


    /**
     * @Route("/historique", name="historique")
     */
    public function historique(CommandeRepository  $repository)
    {
        if (!$this->getUser()) {
            return  $this->redirectToRoute('account_login');
        }
        $mescommande =$repository->historique($this->getUser());
        return $this->render(
            'client/profil/historique.html.twig',
            [
                'mescommande'=>$mescommande
            ]
        );
    }
}
