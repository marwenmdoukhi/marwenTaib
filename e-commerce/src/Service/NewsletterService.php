<?php

namespace App\Service;

use App\Entity\Newsletter;
use App\Event\NewsletterEvent;
use App\Form\NewsletterType;
use App\Repository\NewsletterRepository;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Twig\Environment;

class NewsletterService
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var RequestStack
     */
    private $request;
    /**
     * @var FlashBagInterface
     */
    private $flash;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var NewsletterRepository
     */
    private $repository;

    /**
     * @param FormFactoryInterface $formFactory
     * @param EventDispatcherInterface $eventDisptacher
     * @param EntityManagerInterface $manager
     * @param RequestStack $request
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EventDispatcherInterface $eventDisptacher,
        EntityManagerInterface $manager,
        RequestStack   $request,
        FlashBagInterface $flash,
        Environment $twig,
        NewsletterRepository  $repository
    ) {
        $this->formFactory = $formFactory;
        $this->eventDisptacher = $eventDisptacher;
        $this->manager = $manager;
        $this->request = $request;
        $this->flash = $flash;
        $this->twig = $twig;
        $this->repository = $repository;
    }

    public function newsletter()
    {
        $newsletter=new Newsletter();
        $form=$this->formFactory->create(NewsletterType::class, $newsletter);
        $form->handleRequest($this->request->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $existEmail=$form->get('email')->getData();
            $email =  $this->repository->findOneBy(
                [
                    'email' => $existEmail,
                ]
            );
            if ($email) {
                return new Response(0);
            } else {
                $this->manager->persist($newsletter);
                $this->manager->flush();
                $event = new NewsletterEvent($newsletter);
                $this->eventDisptacher->dispatch($event);
                $this->flash->add('success', "Votre message a eté envoyé");
                return  new Response(1);
            }
        }
        return new Response($this->twig->render('client/newsletter/index.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
