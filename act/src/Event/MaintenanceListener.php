<?php


namespace App\Event;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;


class MaintenanceListener
{
    private $engine;
    public function __construct(Environment $engine)
    {
        $this->engine=$engine;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if($_ENV['STATUS']=='true'){
            return;
        }

        $event->setResponse(
            new Response(
                $this->engine->render('home/maintenance.html.twig'),
                Response::HTTP_SERVICE_UNAVAILABLE
            )
        );
        $event->stopPropagation();
    }
}