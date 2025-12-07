<?php
// src/EventSubscriber/TwigEventSubscriber.php

namespace App\EventSubscriber;

use App\Service\GetBackgroundFooter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;
use App\Service\ImageHelper; // Assurez-vous d'importer votre service ImageHelper

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $imageHelper;
    private $getBackgroundFooter;

    public function __construct(Environment $twig, ImageHelper $imageHelper, GetBackgroundFooter $getBackgroundFooter)
    {
        $this->twig = $twig;
        $this->imageHelper = $imageHelper;
        $this->getBackgroundFooter = $getBackgroundFooter;
    }

    public function onKernelController(ControllerEvent $event)
    {
        // Injecte la variable du logo et du background dans Twig
        $this->twig->addGlobal('logoHtml', $this->imageHelper->getImageDataForTemplate(26, [], "thumbnail", false)['html']);
        $this->twig->addGlobal('backgroundFooter', $this->getBackgroundFooter->getBackground(31));
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
?>