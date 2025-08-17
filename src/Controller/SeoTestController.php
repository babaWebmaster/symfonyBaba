<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SeoTestController extends AbstractController
{
    #[Route('/seo/test', name: 'app_seo_test')]
    public function index(): Response
    {
        return $this->render('seo_test/index.html.twig', [
            'controller_name' => 'SeoTestController',
        ]);
    }
}
