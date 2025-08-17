<?php
// src/EventListener/SecurityLoginListener.php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface; // Indique à Symfony que cette classe "s'abonne" à des événements
use Symfony\Component\HttpFoundation\RequestStack; // Permet d'accéder aux détails de la requête HTTP (comme l'IP ou le User-Agent)
use Symfony\Component\Security\Http\Event\LoginSuccessEvent; // L'événement déclenché par Symfony après une connexion réussie
use Doctrine\ORM\EntityManagerInterface; // L'outil de Doctrine pour interagir avec la base de données (sauvegarder les changements)
use App\Entity\User; // Votre entité utilisateur, où nous allons stocker les informations
use App\Entity\LoginAttempt;// Votre entité pour gérer le succés de la connexion de l'user
use App\Service\IpGeolocationService; // Importez votre service de géolocalisation

class SecurityLoginListener implements EventSubscriberInterface
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private IpGeolocationService $ipGeolocationService;

    // Le constructeur : Symfony détecte les dépendances listées ici et les "injecte" automatiquement.
    // Pas besoin d'appeler ce constructeur manuellement, Symfony gère tout.
    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager, IpGeolocationService $ipGeolocationService)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->ipGeolocationService=$ipGeolocationService;
    }

    /**
     * Méthode OBLIGATOIRE pour un EventSubscriber.
     * Elle dit à Symfony à quels événements notre écouteur est abonné et quelle méthode appeler.
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            // Quand le 'LoginSuccessEvent' est déclenché par Symfony, exécute la méthode 'onLoginSuccess' de cette classe.
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }

    /**
     * Cette méthode est automatiquement appelée par Symfony à chaque connexion réussie.
     * @param LoginSuccessEvent $event L'objet événement contenant des informations sur la connexion.
     */
    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        // 1. On récupère l'objet User (l'utilisateur qui vient de se connecter) depuis l'événement.
        $user = $event->getUser();

        // 2. IMPORTANT : On vérifie que $user est bien une instance de notre entité User.
        // Cela assure que nous avons bien un objet sur lequel on peut appeler nos setters personnalisés.
        if (!$user instanceof User) {
            return; // Si ce n'est pas notre type d'utilisateur, on s'arrête ici.
        }

        // 3. On récupère la requête HTTP actuelle pour obtenir l'IP et le User-Agent.
        $request = $this->requestStack->getCurrentRequest();

        // 4. On s'assure que l'objet Request existe (ce qui est toujours le cas pour une requête HTTP).
        if ($request) {
            // Récupère l'adresse IP du client.
            $ipAddress = $request->getClientIp();

            // Récupère la chaîne User-Agent (informations sur le navigateur et l'OS).
            $userAgent = $request->headers->get('User-Agent');

            //ACTION 1 5. On met à jour les champs de l'entité User avec les informations récupérées.
            // L'entité User est déjà "gérée" par Doctrine puisque l'utilisateur vient de la base de données.
            $user->setLastLoginIp($ipAddress);
            $user->setLastLoginUserAgent($userAgent);

             // ACTION 2 : Enregistrer une nouvelle entrée dans l'historique des tentatives de connexion
            $loginAttempt = new LoginAttempt();
            $loginAttempt->setUser($user);
            $loginAttempt->setIpAddress($ipAddress);
            $loginAttempt->setUserAgent($userAgent);
            $loginAttempt->setLoggedInAt(new \DateTimeImmutable());

             // --- NOUVEAU : Assigner les données de géolocalisation ---
             $geoData = $this->ipGeolocationService->geolocate($ipAddress);
            if ($geoData) {
                $loginAttempt->setCountry($geoData['country']);
                $loginAttempt->setCity($geoData['city']);
                // Ajoutez d'autres setters si vous avez ajouté d'autres champs à votre entité
            }

            // 6. On demande à Doctrine de sauvegarder toutes les modifications en attente en base de données.
            // C'est le 'flush()' qui envoie l'instruction SQL (UPDATE dans ce cas) à la base.
            $this->entityManager->persist($loginAttempt);
            $this->entityManager->flush();
        }
    }
}