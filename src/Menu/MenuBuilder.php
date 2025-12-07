<?php

namespace App\Menu;

use Knp\Menu\Attribute\AsMenuBuilder; 
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MenuBuilder
{
      
    public function __construct( private FactoryInterface $factory, private UrlGeneratorInterface $urlGenerator)
    {
       
    }

    #[AsMenuBuilder(name: 'main_menu')]
    public function createMainMenu(array $options): ItemInterface
    {
         $menu = $this->factory->createItem('main', [
            'childrenAttributes' => [ // <-- Ajoutez ceci à l'élément 'root'
                'id' => 'my-main-menu', // L'ID que vous voulez pour votre <ul>
                'class' => 'navbar-nav ms-auto mb-2 mb-lg-0 col-12 d-flex justify-content-around', // Les classes Bootstrap pour le <ul>
            ],
        ]);

        $menu->addChild('Accueil', ['route' => 'app_home'])
        ->setAttribute('class', 'nav-item')
        ->setLinkAttribute('class','nav-link');
        $menu->addChild('Création site vitrine', ['route' => 'app_creation_site_vitrine'])
        ->setAttribute('class', 'nav-item')
        ->setLinkAttribute('class','nav-link');
        $menu->addChild('Création site wordpress', ['route' => 'app_creation_site_wordpress'])
         ->setAttribute('class', 'nav-item')
        ->setLinkAttribute('class','nav-link');
        $menu->addChild('Création site e commerce', ['route' => 'app_creation_site_commerce']) 
        ->setAttribute('class', 'nav-item')
        ->setLinkAttribute('class','nav-link');
       
        $menu->addChild('Réalisations', [
        'route' => 'app_portfolio',
        'routeParameters' => ['page' => 1]
        ])
        ->setAttribute('class', 'nav-item')
        ->setLinkAttribute('class', 'nav-link');

        $menu->addChild('Contact', ['route' => 'app_contact_devis'])
         ->setAttribute('class', 'nav-item')
        ->setLinkAttribute('class','nav-link');

        


        //active sur une réalisation 
        $menu['Réalisations']->setExtra('routes', [
        ['route' => 'app_portfolio'],
        ['route' => 'app_portfolio_slug']
        ])
        ->setAttribute('class', 'nav-item')
        ->setLinkAttribute('class','nav-link');

       
       
        return $menu;
    }
}

?>