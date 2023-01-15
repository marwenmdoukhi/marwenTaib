<?php

namespace App\Event;

use App\Entity\Newsletter;
use Symfony\Contracts\EventDispatcher\Event;

class NewsletterEvent extends Event
{
    public const TEMPLATE_NEWSLETTER = "email/newsletter.html.twig";
    /**
     * @var Newsletter
     */
    private $newsletter;

    /**
     * @param Newsletter $newsletter
     */
    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    /**
     * @return Newsletter
     */
    public function getNewSletter(): Newsletter
    {
        return $this->newsletter;
    }
}
