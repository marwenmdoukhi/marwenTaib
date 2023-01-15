<?php

namespace App\Controller\Admin;

use App\Entity\Commande;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Spipu\Html2Pdf\Html2Pdf;

/**
 * @Route("/admin")
 */
class AdminPaiementController extends AbstractController
{
    /**
     * @Route("/paiement", name="paiement")
     */
    public function index(CommandeRepository $repository)
    {
        $demandeterminer=$repository->demandeterminer();
        return $this->render('admin/paiement/index.html.twig', [
            'current_link' => 'paiement',
            'demandeterminer'=>$demandeterminer

        ]);
    }


    /**
     * @Route("/payer/{id}", name="payer")
     * @param Request $request
     * @param $id
     */
    public function demandeaccepter(Request $request, $id, CommandeRepository $repository)
    {
        $em = $this->getDoctrine()->getManager();
        $demande = new Commande();
        $demande = $repository->find($id);
        if ($demande->isPayer()) {
            $demande->setPayer(0);
        } else {
            $demande->setPayer(1);
            $demande->setDatedepaimenet(new \DateTime('now'));
            $em->persist($demande);
            $em->flush();
        }
        return $this->render('admin/paiement/facture.html.twig');
    }

    /**
     * @Route("detail/{id}", name="detailfacutre")
     */
    public function detail($id)
    {
        $em=$this->getDoctrine()->getManager();
        $query = $em->createQuery(
            ' SELECT c
            FROM App\Entity\Commande c , App\Entity\CommandeArticle ca
            where c.id =:uc 
                  '
        );
        $query->SetParameters(array('uc' => $id));
        $factures = $query->getOneOrNullResult();
        $query1 = $em->createQuery(
            ' SELECT c.Numero
            FROM App\Entity\Commande c 
            where c.id =:uc 
                  '
        );
        $query1->SetParameters(array('uc' => $id));
        $numero = $query1->getSingleScalarResult();

        $html = $this->renderView('admin/paiement/detail.html.twig', array('facture' => $factures,'numero'=>$numero));
        $html2pdf = new Html2Pdf();

        $html2pdf->pdf->SetAuthor('LOOH');
        $html2pdf->pdf->SetTitle('Facture'." ".$numero);

        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output('Facture.pdf');
        $response= new Response();
        $response->headers->set('Content-type', 'application/pdf');
        return $response;
    }
}
