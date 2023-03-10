<?php

namespace App\Controller\Admin;

use App\Entity\Marque;
use App\Form\MarqueType;
use App\Repository\MarqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/marque")
 */
class MarqueController extends AbstractController
{
    /**
     * @Route("/", name="marque_index", methods={"GET"})
     * @param MarqueRepository $marqueRepository
     * @return Response
     */
    public function index(MarqueRepository $marqueRepository): Response
    {
        return $this->render('admin/marque/index.html.twig', [
            'marques' => $marqueRepository->findAll(),
            'current_link' => 'marques',

        ]);
    }

    /**
     * @Route("/new", name="marque_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $marque = new Marque();
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($marque);
            $entityManager->flush();

            return $this->redirectToRoute('marque_index');
        }

        return $this->render('admin/marque/new.html.twig', [
            'marque' => $marque,
            'form' => $form->createView(),
            'current_link' => 'marques',

        ]);
    }

    /**
     * @Route("/{id}", name="marque_show", methods={"GET"})
     */
    public function show(Marque $marque): Response
    {
        return $this->render('admin/marque/show.html.twig', [
            'marque' => $marque,
            'current_link' => 'marques',

        ]);
    }

    /**
     * @Route("/{id}/edit", name="marque_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Marque $marque): Response
    {
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('marque_index');
        }

        return $this->render('admin/marque/edit.html.twig', [
            'marque' => $marque,
            'form' => $form->createView(),
            'current_link' => 'marques',

        ]);
    }





    /**
     * @Route("/delete/{id}", name="marque_delete")
     */
    public function delete(Request $request, Marque $marque): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($marque);
        $entityManager->flush();

        return $this->redirectToRoute('marque_index');
    }
}
