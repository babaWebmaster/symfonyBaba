<?php
// src/Controller/SecurityController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    // LA ROUTE POUR AFFICHER LE FORMULAIRE ET POUR TRAITER SA SOUMISSION
    #[Route(path: '/admin-baba-5487894613734/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // ... (le code standard généré par make:security:form)
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // Assurez-vous que CE CHEMIN VERS LE TEMPLATE EST CORRECT
        // Si make:security:form a créé templates/security/login.html.twig :
        return $this->render('security/login.html.twig', [
                    'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    
    // LA ROUTE DE DÉCONNEXION
    #[Route(path: '/admin-baba-5487894613734/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key in your security.yaml.');
    }
}