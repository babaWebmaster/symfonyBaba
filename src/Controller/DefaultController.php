<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ImageHelper;



final class DefaultController extends AbstractController
{
    use SeoAwareControllerTrait; 

    public function __construct(private ImageHelper $imageHelper)      
    {
    }
    
   /* #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
      
      /*  $pageSlug = 'accueil'; // <<< LE SLUG SPÉCIFIQUE À CETTE PAGE STATIQUE >>>

        $seoCriteria = [
            'postType' => 'static_page',
            'slug' => $pageSlug,
        ];

        $seoPost = $this->getSeoPostFromCriteria($entityManager, $seoCriteria);
*/
      /*   $imageData = $this->imageHelper->getImageDataForTemplate(25,["class"=>"image"],'thumbnail',false);

        return $this->render('default/index.html.twig', [ // Votre template pour cette page
            'seoPost' => $this->getSeoMeta($entityManager),
            'image_data'=> $imageData,
            'test_image_html'=> $imageData['html'],
            'controller_name'=>"defaultController"
        ]);
    }*/
    /**
     * Récupère les meta données de la page accueil
     */

    public function getSeoMeta($entityManager)
    {
        $pageSlug = 'accueil'; // <<< LE SLUG SPÉCIFIQUE À CETTE PAGE STATIQUE >>>

        $seoCriteria = [
            'postType' => 'static_page',
            'slug' => $pageSlug,
        ];

        return $this->getSeoPostFromCriteria($entityManager, $seoCriteria);
    }
     /**
     * Récupère une image et génère son HTML formaté pour la vue.
     * @param int $imageId L'ID de l'image à récupérer.
     * @param array $attributes Attributs HTML additionnels pour la balise <img>.
     * @return array Un tableau contenant le HTML de l'image et d'autres données.
     */

    public function getImageDataForTemplate(int $imageId, array $attributes = [])
    {
        // récupérer l'image par son ID
        $image = $this->imageRepository->find($imageId);

         // Si l'image n'existe pas, vous pouvez retourner des valeurs par défaut ou gérer l'erreur
        if (!$image) {
            // Pour l'exemple, retournons un HTML vide et un indicateur
            return [
                'html' => '<p>Image non trouvée.</p>',
                'found' => false,
                'id' => null,
                'originalName' => null
            ];
        }

        // Définir les attributs par défaut si non fournis
        if (empty($attributes)) {
            $attributes = [
                'class' => 'generated-image',
                'data-id' => $image->getId(),
            ];
        }

        // Générer le HTML de l'image en utilisant le service ImageHtmlBuilder
        $imageHtml = $this->imageHtmlBuilder->buildImageTag(
            $image,
            $image->getAltText() ?: $image->getOriginalName(),
            $attributes
        );

        // Retourner un tableau avec toutes les données nécessaires à la vue
        return [
            'html' => $imageHtml,
            'found' => true,
            'id' => $image->getId(),
            'originalName' => $image->getOriginalName(),
            // Vous pouvez ajouter d'autres propriétés de l'objet image si la vue en a besoin
            'image_object' => $image // Passe l'objet Image entier si la vue a besoin d'accéder à toutes ses propriétés
        ];
    }

}


