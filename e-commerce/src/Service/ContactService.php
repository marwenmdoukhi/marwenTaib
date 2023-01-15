<?php

namespace App\Service;

use App\Entity\Contact;
use App\Event\ContactEvent;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Void_;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class ContactService
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
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Environment
     */
    private $twig;

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
        RouterInterface $router,
        Environment $twig
    ) {
        $this->formFactory = $formFactory;
        $this->eventDisptacher = $eventDisptacher;
        $this->manager = $manager;
        $this->request = $request;
        $this->flash = $flash;
        $this->router = $router;
        $this->twig = $twig;
    }

    public function contact()
    {
        $contact = new Contact();
        $form=$this->formFactory->create(ContactType::class, $contact);
        $form->handleRequest($this->request->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $event = new ContactEvent($contact);
            $this->eventDisptacher->dispatch($event);
            $this->flash->add('success', "Votre message a eté envoyé");
            return new RedirectResponse($this->router->generate('contact'));
        }
        return new Response($this->twig->render('client/contact/index.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
