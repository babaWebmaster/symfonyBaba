<?php

namespace App\Service;

use App\Service\ImageHtmlBuilder;
use App\Entity\Image;
use App\Repository\ImageRepository;

class ImageHelper
{

    

    public function __construct(private ImageHtmlBuilder $imageHtmlBuilder,private ImageRepository $imageRepository)
    {

    }

    /**
     * Prépare les données d'une image pour être utilisées dans un template Twig,
     * en récupérant l'image par son ID.
     *
     * @param int $imageId L'ID de l'entité Image à récupérer.
     * @param string $altText Texte alternatif à utiliser si l'image n'en a pas.
     * @param array $attributes Attributs HTML supplémentaires pour la balise img/picture.
     * @param string $srcFormat Le format de l'image pour l'attribut src principal.
     * @return array Un tableau de données prêtes à être passées au template, incluant le HTML généré.
     */

    public function getImageDataForTemplate(int $imageId, array $attributes = [], string $srcFormat='original',bool $useResponsiveAttributes = true)
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
            $attributes,
            $srcFormat,
            $useResponsiveAttributes
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

?>