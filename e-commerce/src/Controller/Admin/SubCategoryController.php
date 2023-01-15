<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Subcategory;
use App\Form\SubCategoryType;
use App\Repository\SubcategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @Route("/admin/subcategory")
 */
class SubCategoryController extends AbstractController
{
    /**
     * @Route("/", name="sub_category_index", methods={"GET"})
     * @param SubcategoryRepository $categoryRepository
     * @return Response
     */
    public function index(SubcategoryRepository $categoryRepository, CacheInterface $cache): Response
    {
        $categories = $cache->get('subcategories', function (ItemInterface $item) use ($categoryRepository) {
            $item->expiresAfter(3600);
            return $categoryRepository->findAll();
        });
        return $this->render('admin/subcategory/index.html.twig', [
            'categories'=> $categories,
            'current_link' => 'subcategories',

        ]);
    }

    /**
     * @Route("/new", name="sub_category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $category = new Subcategory();
        $form = $this->createForm(SubCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('picture')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('img_category'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $category->setPicture($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('sub_category_index');
        }
        return $this->render('admin/subcategory/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'current_link' => 'subcategories',
        ]);
    }

    /**
     * @Route("/{id}", name="sub_category_show", methods={"GET"})
     */
    public function show(Category $category): Response
    {
        return $this->render('admin/subcategory/show.html.twig', [
            'category' => $category,
            'current_link' => 'subcategories',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sub_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Subcategory $category): Response
    {
        $form = $this->createForm(SubCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('picture')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('img_category'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $category->setPicture($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sub_category_index');
        }

        return $this->render('admin/subcategory/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'current_link' => 'subcategories',

        ]);
    }

    /**
     * @Route("/delete/{id}", name="sub_category_delete")
     */
    public function delete(Request $request, Subcategory $category): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();
        return $this->redirectToRoute('sub_category_index');
    }
}
