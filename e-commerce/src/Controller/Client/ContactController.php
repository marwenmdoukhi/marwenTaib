<?php

namespace App\Controller\Client;

use App\Service\ContactService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @var ContactService
     */
    private $service;

    /**
     * @param ContactService $service
     */
    public function __construct(ContactService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function __invoke()
    {
        return$this->service->contact();
    }
}
