<?php
// src/EventListener/SecurityLoginFailureListener.php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\LoginFailure;
use App\Service\IpGeolocationService; 
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class SecurityLoginFailureListener implements EventSubscriberInterface
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private IpGeolocationService $ipGeolocationService; // NOUVEAU : Déclarez la propriété

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager, IpGeolocationService $ipGeolocationService) 
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->ipGeolocationService = $ipGeolocationService; 
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginFailureEvent::class => 'onLoginFailure',
        ];
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return;
        }

        $ipAddress = $request->getClientIp();
        $userAgent = $request->headers->get('User-Agent');
        $passport = $event->getPassport();
        $usernameAttempted = 'unknown_identifier';//valeur par default


        // Tenter de récupérer le UserBadge, qui contient l'identifiant tenté
         if ($passport->hasBadge(UserBadge::class)) {
        /** @var UserBadge $userBadge */
        $userBadge = $passport->getBadge(UserBadge::class);
        $usernameAttempted = $userBadge->getUserIdentifier();
        }

        $exception = $event->getException();
        $failureReason = $exception->getMessage();

        // --- NOUVEAU : Récupérer les données de géolocalisation ---
        $geoData = $this->ipGeolocationService->geolocate($ipAddress);
        // --- FIN NOUVEAU ---

        $loginFailure = new LoginFailure();
        $loginFailure->setUsernameAttempted($usernameAttempted);
        $loginFailure->setIpAddress($ipAddress);
        $loginFailure->setUserAgent($userAgent);
        $loginFailure->setAttemptedAt(new \DateTimeImmutable());
        $loginFailure->setFailureReason($failureReason);

        // --- NOUVEAU : Assigner les données de géolocalisation ---
        if ($geoData) {
            $loginFailure->setCountry($geoData['country']);
            $loginFailure->setCity($geoData['city']);
            // Ajoutez d'autres setters si vous avez ajouté d'autres champs
        }
        // --- FIN NOUVEAU ---

        $this->entityManager->persist($loginFailure);
        $this->entityManager->flush();
    }
}