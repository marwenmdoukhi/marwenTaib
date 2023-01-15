<?php

namespace App\Controller\Client;

use App\Service\NewsletterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController
{
    /**
     * @var NewsletterService
     */
    private $service;

    /**
     * @param NewsletterService $service
     */
    public function __construct(NewsletterService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("newsletter", name="newsletter")
     * @param Request $request
     * @return Response
     */
    public function __invoke()
    {
        return  $this->service->newsletter();
    }
}
