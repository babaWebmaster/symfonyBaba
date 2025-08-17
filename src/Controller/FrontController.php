<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
//use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use App\Service\ImageHelper;
use App\Service\GetBackgroundFooter;


final class FrontController extends AbstractController
{

    public function __construct(private ImageHelper $imageHelper)
    {
       
    }

    #[Route('/', name: 'app_home')]
    public function index(GetBackgroundFooter $getBackgroundFooter, ): Response
    {
       
        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
            'logoHtml' => $this->imageHelper->getImageDataForTemplate(26,[],"thumbnail",false)['html'],
            'backgroundFooter' => $getBackgroundFooter->getBackground(27),
            
        ]);
    }

    #[Route('/creation-site-vitrine-paris', name: 'app_creation_site_vitrine')]
    public function createSiteVitrine(): Response
    {
        
    }

    #[Route('/creation-site-wordpress-paris', name: 'app_creation_site_wordpress')]
    public function createSiteWordpress(): Response
    {
        
    }

    #[Route('/creation-site-e-commerce', name: 'app_creation_site_commerce')]
    public function createSiteCommerce(): Response
    {
        
    }

    #[Route('/contact', name: 'app_contact_devis')]
    public function contactDevis(): Response
    {
        
    }

    
}
