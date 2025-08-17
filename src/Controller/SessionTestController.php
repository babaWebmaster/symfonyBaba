<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request; // N'oubliez pas d'importer Request !
use Symfony\Component\Routing\Attribute\Route;

class SessionTestController extends AbstractController
{
    #[Route('/session_test', name: 'app_session_test')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();

        // Incrémente un compteur dans la session à chaque visite
        $counter = $session->get('page_visits', 0) + 1;
        $session->set('page_visits', $counter);

        // Enregistre un message de test
        $session->set('test_message', 'Ceci est un message de session stocké en DB !');

        // Récupère les valeurs pour affichage
        $storedCounter = $session->get('page_visits');
        $storedMessage = $session->get('test_message');

        return $this->render('session_test/index.html.twig', [
            'controller_name' => 'SessionTestController',
            'counter' => $storedCounter,
            'message' => $storedMessage,
        ]);
    }

    #[Route('/session_clear', name: 'app_session_clear')]
    public function clear(Request $request): Response
    {
        $session = $request->getSession();
        $session->clear(); // Efface toutes les données de la session

        $this->addFlash('success', 'Session effacée avec succès !');

        return $this->redirectToRoute('app_session_test');
    }
}
