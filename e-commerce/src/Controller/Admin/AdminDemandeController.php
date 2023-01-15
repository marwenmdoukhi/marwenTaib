<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/demande")
 */
class AdminDemandeController extends AbstractController
{
    /**
     * @var CommandeRepository
     */
    private $commandeRepository;

    public function __construct(CommandeRepository $commandeRepository)
    {
        $this->commandeRepository = $commandeRepository;
    }
    /**
     * @Route("/", name="demande")
     */
    public function index()
    {
        $toutlesdemande=  $this->commandeRepository->findAll();
        $demandes=  $this->commandeRepository->demandes();
        $demandeaccepter=  $this->commandeRepository->demandeaccepter();
        $demandeterminer=  $this->commandeRepository->demandeterminer();
        return $this->render('admin/demande/index.html.twig', [
            'current_link' => 'demande',
            'demandes'=>$demandes,
            'toutlesdemande'=>$toutlesdemande,
            'demandeaccepter'=>$demandeaccepter,
            'demandeterminer'=>$demandeterminer,
        ]);
    }

    /**
     * @Route("/demandeaccepter/{id}", name="demandeacceper")
     * @param Request $request
     * @param $id
     */
    public function demandeaccepter(Request $request, $id, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $demande = $em->getRepository('App:Commande')->find($id);
        $email=$demande->users->getEmail();
        if ($demande->getStatus()) {
            $demande->setStatus(0);
        } else {
            $demande->setStatus(1);
            $em->persist($demande);
            $em->flush();
//            $message = (new \Swift_Message('demande accepter'))
//                ->setFrom('config ')
//                ->setTo($demande->users->getEmail());
//            $message->setBody(
//                $this->renderView(
//                    'email/demainderAccepter.html.twig',
//                    ['detailcommande' => $demande]
//                ), 'text/html'
//            );
//            try {
//                $mailer->send($message);
//            } catch (FileException $e) {
//                return new Response(0);
//            }

            return $this->redirectToRoute('demande');
        }
        return $this->render('admin/demande/index.html.twig');
    }


    /**
     * @Route("/demandeterminer/{id}", name="demandeterminer")
     * @param Request $request
     * @param $id
     */
    public function demandeterminer(Request $request, $id, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $demande = $em->getRepository('App:Commande')->find($id);
        if ($demande->isTerminer()) {
            $demande->setTerminer(0);
        } else {
            $demande->setTerminer(1);
            $demande->setPayer(1);
            $demande->setDatedepaimenet(new \DateTime());
            $em->persist($demande);
            $em->flush();
//            $message = (new \Swift_Message('demande Terminer'))
//                ->setFrom('config ')
//                ->setTo($demande->users->getEmail());
//            $message->setBody(
//                $this->renderView(
//                    'email/demaindeTerminer.html.twig',
//                    ['detailcommande' => $demande]
//                ), 'text/html'
//            );
//            try {
//                $mailer->send($message);
//            } catch (FileException $e) {
//                return new Response(0);
//            }
            return $this->redirectToRoute('detaildemande', array(
                'id' => $demande->getId()));
        }
        return $this->render('admin/demande/index.html.twig');
    }

    /**
     * @Route("/{id}", name="detaildemande")
     */
    public function detail($id, CommandeRepository $commandeRepository)
    {
        $info=$commandeRepository->findOneById($id);
        return $this->render('admin/demande/detail.html.twig', [
            'current_link' => 'demande',
            'info'=>$info,
            'detaildemande'=>$commandeRepository->detaildemande($id),

        ]);
    }

    /**
     * @Route("/delete/{id}", name="demande_delete")
     */
    public function delete(Request $request, Commande $commande): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($commande);
        $entityManager->flush();

        return $this->redirectToRoute('demande');
    }
}
