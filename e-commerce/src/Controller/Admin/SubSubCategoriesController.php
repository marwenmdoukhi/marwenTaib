<?php

namespace App\Controller\Admin;

use App\Entity\SubSubCategories;
use App\Form\SubSubCategoriesType;
use App\Repository\SubSubCategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/subsubcategories")
 */
class SubSubCategoriesController extends AbstractController
{
    /**
     * @Route("/", name="subsubcategories_index", methods={"GET"})
     */
    public function index(SubSubCategoriesRepository $subSubCategoriesRepository): Response
    {
        return $this->render('admin/subsubcategories/index.html.twig', [
            'sub' => $subSubCategoriesRepository->findAll(),
            'current_link' => 'subsubcategories',
        ]);
    }

    /**
     * @Route("/new", name="subsubcategories_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $sub = new SubSubCategories();
        $form = $this->createForm(SubSubCategoriesType::class, $sub);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('picture')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('img_subcategory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $sub->setPicture($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sub);
            $entityManager->flush();
            return $this->redirectToRoute('subsubcategories_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/subsubcategories/new.html.twig', [
            'subsubcategories' => $sub,
            'form' => $form->createView(),
            'current_link' => 'subsubcategories',

        ]);
    }


    /**
     * @Route("/{id}/edit", name="subsubcategories_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SubSubCategories $sub): Response
    {
        $form = $this->createForm(SubSubCategoriesType::class, $sub);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('picture')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('img_subcategory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $sub->setPicture($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('subsubcategories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/subsubcategories/edit.html.twig', [
            'subsubcategories' => $sub,
            'form' => $form->createView(),
            'current_link' => 'subsubcategories',

        ]);
    }

    /**
     * @Route("/delete/{id}", name="subsubcategories_delete")
     */
    public function delete(Request $request, SubSubCategories $sub): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($sub);
        $entityManager->flush();

        return $this->redirectToRoute('subsubcategories_index', [], Response::HTTP_SEE_OTHER);
    }
}
