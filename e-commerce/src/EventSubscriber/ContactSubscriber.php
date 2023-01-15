<?php

namespace App\EventSubscriber;

use App\Event\ContactEvent;
use App\Service\SwiftMailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContactSubscriber implements EventSubscriberInterface
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
     * @param ContactEvent $event
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function onSendContact(ContactEvent $event)
    {
        $contact = $event->getContact();
        $parameters = [
            "name" => $contact->getFirstName(),
            "phone" => $contact->getPhone(),
            "email" => $contact->getEmail(),
            "sujet" => $contact->getSujet(),
            "message" => $contact->getMessage(),
        ];

        $this->mailerService->send(
            "Nouveau Contact",
            [$contact->getEmail()],
            ['contact@fmw-store.tn'],
            ContactEvent::TEMPLATE_CONTACT,
            $parameters
        );
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ContactEvent::class => [
                ['onSendContact', 1]
            ]
        ];
    }
}
