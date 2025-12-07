<?php

namespace App\Controller;

use App\Service\ImageHelper;
use App\Service\GetBackgroundFooter;
use App\Service\JsonDataReader;
use App\Service\ContactFormHandler;
use App\Service\SeoService;
use App\Service\ImagePreviewBuilderMaquette;
use App\Service\CacheResponseService;
use App\Form\ContactFormType;
use App\Repository\MaquetteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;




final class FrontController extends AbstractController
{

    public function __construct(private ImageHelper $imageHelper, private JsonDataReader $jsonDataReader, private SeoService $seoService, private MaquetteRepository $maquetteRepository, private CacheResponseService $cacheResponseService)
    {
       
    }

    #[Route('/', name: 'app_home')]
    public function index(ImagePreviewBuilderMaquette $imagePreviewBuilderMaquette): Response
    {
        
         $maquettes = $this->maquetteRepository->findResultWidthCategoriesWidthLimit(3);

        // récupération du html de l'image de la vignette
        $arrayImages = [];
        
         foreach($maquettes as $image)
            {
               
            $arrayImages[$image['id']]['htmlImage'] = $imagePreviewBuilderMaquette->buildUrlFromId($image['imagePreview']);
               
            };
       
        $response = $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
            'background_presentation' => $this->imageHelper->getImageDataForTemplate(30),
            'page_data'=> $this->jsonDataReader->getData('index.json'),
            'maquettes' => $maquettes,
            'images' => $arrayImages,
            'seoPost' => $this->seoService->getSeoPostFromCriteria(['slug'=>'accueil'])           
        ]);

        return $this->cacheResponseService->applyCache($response);
    }

    #[Route('/creation-site-vitrine-paris', name: 'app_creation_site_vitrine')]
    public function createSiteVitrine(ImagePreviewBuilderMaquette $imagePreviewBuilderMaquette): Response
    { 

        $maquettes = $this->maquetteRepository->findByCategoryName('Vitrine', 3);

        // récupération du html de l'image de la vignette
        $arrayImages = [];
        
         foreach($maquettes as $image)
            {
               
            $arrayImages[$image['id']]['htmlImage'] = $imagePreviewBuilderMaquette->buildUrlFromId($image['imagePreview']);
               
            };


        $response = $this->render('front/creation_site_vitrine.html.twig', [
            'controller_name' => 'FrontController',
            'background_creation_vitrine' => $this->imageHelper->getImageDataForTemplate(33),
            'page_data'=> $this->jsonDataReader->getData('creation_site_vitrine.json'),
            'maquettes' => $maquettes,
            'images' => $arrayImages,
            'seoPost' => $this->seoService->getSeoPostFromCriteria(['slug'=>'site_vitrine'])         
        ]);

        return $this->cacheResponseService->applyCache($response);
    }

    #[Route('/creation-site-wordpress-paris', name: 'app_creation_site_wordpress')]
    public function createSiteWordpress(ImagePreviewBuilderMaquette $imagePreviewBuilderMaquette): Response
    {
            $maquettes = $this->maquetteRepository->findByCategoryName('Wordpress', 3);

        // récupération du html de l'image de la vignette
        $arrayImages = [];
        
         foreach($maquettes as $image)
            {
               
            $arrayImages[$image['id']]['htmlImage'] = $imagePreviewBuilderMaquette->buildUrlFromId($image['imagePreview']);
               
            };

        $response = $this->render('front/creation_site_wordpress.html.twig', [
            'controller_name' => 'FrontController',
            'background_wordpress' => $this->imageHelper->getImageDataForTemplate(34),
            'page_data'=> $this->jsonDataReader->getData('creation_site_wordpress.json'),
            'maquettes' => $maquettes,
            'images' => $arrayImages,
            'seoPost' => $this->seoService->getSeoPostFromCriteria(['slug'=>'site_wordpress'])            
        ]);

        return $this->cacheResponseService->applyCache($response);
        
    }

    #[Route('/creation-site-e-commerce', name: 'app_creation_site_commerce')]
    public function createSiteCommerce(ImagePreviewBuilderMaquette $imagePreviewBuilderMaquette): Response
    {

            $maquettes = $this->maquetteRepository->findByCategoryName('E-commerce', 3);

        // récupération du html de l'image de la vignette
        $arrayImages = [];
        
         foreach($maquettes as $image)
            {
               
            $arrayImages[$image['id']]['htmlImage'] = $imagePreviewBuilderMaquette->buildUrlFromId($image['imagePreview']);
               
            };

        $response = $this->render('front/creation_site_ecommerce.html.twig', [
            'controller_name' => 'FrontController',
            'background_eCommerce' => $this->imageHelper->getImageDataForTemplate(35),
            'page_data'=> $this->jsonDataReader->getData('creation_site_commerce.json'),
            'maquettes' => $maquettes,
            'images' => $arrayImages,
            'seoPost' => $this->seoService->getSeoPostFromCriteria(['slug'=>'site_e_commerce'])            
        ]);

        return $this->cacheResponseService->applyCache($response);
        
    }

    #[Route('/contact', name: 'app_contact_devis')]
    public function contactDevis(Request $request, ContactFormHandler $contactFormHandler): Response
    {
        $messageError="";
        $form= $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        

        if ($form->isSubmitted() && $form->isValid()) {
        $contactFormHandler->handle($form); // On ne vérifie pas le retour, on sait que ça a marché
         $this->addFlash('success', 'Votre message a bien été envoyé !');
        
                // Redirigez vers la même page.
                return $this->redirectToRoute('app_contact_devis');
        }
      



            if($form->isSubmitted() && !$form->isValid()){
                    $this->addFlash('error', 'Votre message n\'a pu être transmis. Veuillez vérifiez vos informations saisies .');
            }
        


        $response = $this->render('front/contact_devis.html.twig', [
            'controller_name' => 'FrontController',
            'contactForm' => $form->createView(),
            'background_presentation' => $this->imageHelper->getImageDataForTemplate(30),
            'page_data'=> $this->jsonDataReader->getData('creation_site_vitrine.json'),
            'seoPost' => $this->seoService->getSeoPostFromCriteria(['slug'=>'contact'])         
        ]);

        return $this->cacheResponseService->applyCache($response);
        
    }

    #[Route('/mention-legale', name: 'app_mention_legale')]
    public function mentionLegale(): Response
    {
        $response = $this->render('front/mention_legale.html.twig', [
            'controller_name' => 'FrontController',
            'background_presentation' => $this->imageHelper->getImageDataForTemplate(30),
            'page_data'=> $this->jsonDataReader->getData('mention_legale.json'),
            'seoPost' => $this->seoService->getSeoPostFromCriteria(['slug'=>'mention_legale'])           
        ]);

        return $this->cacheResponseService->applyCache($response);
        
    }

     #[Route('/portfolio/page/{page}', name: 'app_portfolio')]
    public function portfolio(int $page = 1 , PaginatorInterface $paginator,ImagePreviewBuilderMaquette $imagePreviewBuilderMaquette): Response
    {
        $query = $this->maquetteRepository->getAllQueryBuilder();
        $pagination = $paginator->paginate($query, $page, 12);
        

        //redirection si la page inexistante
        if ($pagination->getTotalItemCount() > 0 && count($pagination) === 0 && $page > 1) {
                return $this->redirectToRoute('app_portfolio', ['page' => $pagination->getPaginationData()['last']]);
            }

            // récupération du html de l'image de la vignette
            $arrayImages = [];

            foreach($pagination as $v){
                                 
             $arrayImages[$v['id']]['htmlImage'] =  $imagePreviewBuilderMaquette->buildUrlFromId($v['imagePreview']);
             
            }
            
          

        $response = $this->render('front/portfolio.html.twig', [
            'controller_name' => 'FrontController',
            'background_presentation' => $this->imageHelper->getImageDataForTemplate(30),
            'pagination' => $pagination,
            'images' => $arrayImages,
            'seoPost' => $this->seoService->getSeoPostFromCriteria(['slug'=>'portfolio'])           
        ]);

        return $this->cacheResponseService->applyCache($response);
        
    }

    #[Route('/portfolio/{slug}', name: 'app_portfolio_slug')]
    public function portfolioSlug(string $slug, ImageHelper $imageHelper,ImagePreviewBuilderMaquette $imagePreviewBuilderMaquette ): Response
    {
                  
          $maquette = $this->maquetteRepository->findOneWidthCategories($slug);


         $maquette->setUrlImagePreview($imagePreviewBuilderMaquette-> buildUrlsFromIds($maquette->getImagePreview()));
                  

          if(!$maquette){
            throw $this->createNotFoundException('La réalisation de cette site n\'est pas disponible');
          }

          $categoriesSite = $maquette->getCategoriesSite()->toArray();
          
 
        $response = $this->render('front/portfolio_slug.html.twig', [
            'controller_name' => 'FrontController',
            'maquette' => $maquette,
            'category' => $categoriesSite[array_rand($categoriesSite)],
            'background_presentation' => $this->imageHelper->getImageDataForTemplate(30),
            'seoPost' => $this->seoService->getSeoPostFromCriteria(['entityId'=>$maquette->getId(),
            'postType' => 'maquette']) 
                
        ]);

        return $this->cacheResponseService->applyCache($response);
        
    }

    #[Route('/iframe_portofolio/{slugIframe}', name: 'app_iframe_portfolio')]
    public function portfolioIframe(Request $request ,string $slugIframe): Response
    {
            $url = $request->query->get('url');   
        
            $response = $this->render('front/portfolio_iframe.html.twig', [
            'controller_name' => 'FrontController',
            'url' => $url,
            'slug' => $slugIframe,
            'background_presentation' => $this->imageHelper->getImageDataForTemplate(30),
                
        ]);

        return $this->cacheResponseService->applyCache($response);
        
    }
    

    
}
