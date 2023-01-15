<?php

namespace App\EventSubscriber;

use App\Event\NewsletterEvent;
use App\Service\SwiftMailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewsletterSubscriber implements EventSubscriberInterface
{
    /**
     * @var SwiftMailerService
     */
    protected $mailerService;

    public function __construct(SwiftMailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    /**
     * @param NewsletterEvent $event
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function onSendNewsletter(NewsletterEvent $event)
    {
        $newsletter = $event->getNewSletter();

        $parameters = [
            "email" => $newsletter->getEmail(),
        ];

        $this->mailerService->send(
            "newsletter",
            ['contact@fmw-store.tn'],
            [$newsletter->getEmail()],
            NewsletterEvent::TEMPLATE_NEWSLETTER,
            $parameters
        );
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            NewsletterEvent::class => [
                ['onSendNewsletter', 1]
            ]
        ];
    }
}
