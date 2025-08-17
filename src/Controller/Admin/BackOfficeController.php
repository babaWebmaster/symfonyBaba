<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted; // Importez IsGranted pour la sécurité
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

//#[Route('/back-office')] // Préfixe toutes les routes de ce contrôleur par /back-office
#[IsGranted('ROLE_ADMIN')] // Exige que l'utilisateur ait le rôle ROLE_ADMIN pour accéder à ce contrôleur
class BackOfficeController extends AbstractController
{
    /**
     * Affiche le tableau de bord du back-office.
     */
   /* #[Route('/', name: 'back_office_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('back_office/admin/dashboard.html.twig', [
            'controller_name' => 'BackOfficeController',
            'current_page' => 'dashboard', // Pour marquer le menu actif
        ]);
    }
*/
   

    /**
     * Affiche les logs de sécurité (tentatives de connexion et échecs).
     */
    #[Route('/security-logs', name: 'back_office_security_logs')]
    public function securityLogs(): Response
    {
        // Ici, vous récupéreriez les LoginAttempt et LoginFailure
        // Pour l'instant, c'est juste un placeholder.
        $loginAttempts = []; // Remplacer par $loginAttemptRepository->findAll();
        $loginFailures = []; // Remplacer par $loginFailureRepository->findAll();

        return $this->render('back_office/security_logs.html.twig', [
            'loginAttempts' => $loginAttempts,
            'loginFailures' => $loginFailures,
            'current_page' => 'security_logs', // Pour marquer le menu actif
        ]);
    }

    /**
     * Affiche la page des paramètres du back-office.
     */
    #[Route('/settings', name: 'back_office_settings')]
    public function settings(): Response
    {
        return $this->render('back_office/settings.html.twig', [
            'current_page' => 'settings', // Pour marquer le menu actif
        ]);
    }
}