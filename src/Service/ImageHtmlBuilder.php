<?php

namespace App\Service;

use App\Entity\Image;
use Symfony\Component\Asset\Packages;

class ImageHtmlBuilder
{
    private string $uploadDirBaseUrl;
    private Packages $assetPackages;

    public function __construct(Packages $assetPackages)
    {
        $this->uploadDirBaseUrl = '/uploads/images/'; // Votre chemin de base pour les images
        $this->assetPackages = $assetPackages;
    }

    /**
     * Génère la balise <picture> ou <img> avec srcset/sizes pour une image donnée.
     *
     * @param Image  $image      L'entité Image.
     * @param string $altText    Le texte alternatif pour l'image.
     * @param array  $attributes Un tableau associatif d'attributs HTML (e.g., ['class' => 'my-class', 'data-id' => '123']).
     * @return string La balise HTML <picture> ou <img> générée.
     */
    public function buildImageTag(Image $image, string $altText = '', array $attributes = [], string $srcFormat='original',bool $useResponsiveAttributes = true): string
    {
        $formats = $image->getFormats();
        $baseFileName = $image->getFileName();

         
        //Déterminer l'URL pour l'attribut 'src' selon le format indiqué dans les arguments
        $finalSrc = $this->getImageUrlByFormat($srcFormat, $baseFileName, $formats);
      
        // --- Génération des attributs supplémentaires ---
        $extraAttributes = $this->buildAttributes($attributes);
        // alt texte
        $imageAltText = $altText ?: $image->getAltText() ?: $image->getOriginalName();    

         // si $useResponsiveAttributes est false
         if(!$useResponsiveAttributes)
         {
            return sprintf(
                '<img src="%s" alt="%s" %s loading="lazy">',
                htmlspecialchars($finalSrc),
                htmlspecialchars($imageAltText),
                $extraAttributes
            );
         }
    
        // --- Construction du srcset ---
        $srcsetParts = [];
        // Assurez-vous que les largeurs (150w, 600w, 1200w) correspondent à vos formats réels
        if (isset($formats['thumbnail'])) {
            $srcsetParts[] = $this->assetPackages->getUrl($formats['thumbnail'], 'uploads_images') . ' 150w';
        }
        if (isset($formats['medium'])) {
            $srcsetParts[] = $this->assetPackages->getUrl($formats['medium'], 'uploads_images') . ' 600w';
        }
        if (isset($formats['large'])) {
            $srcsetParts[] = $this->assetPackages->getUrl($formats['large'], 'uploads_images') . ' 1200w';
        }
        if ($baseFileName) {
            // Si vous avez la largeur originale stockée dans l'entité Image
            $originalWidth = method_exists($image, 'getWidth') ? $image->getWidth() : null;
            if ($originalWidth) {
                $srcsetParts[] = $this->assetPackages->getUrl($baseFileName, 'uploads_images') . ' ' . $originalWidth . 'w';
            } else {
                // Fallback si la largeur n'est pas disponible, ou si c'est déjà l'image de référence pour src
                $srcsetParts[] = $this->assetPackages->getUrl($baseFileName, 'uploads_images');
            }
        }
        $srcset = implode(', ', $srcsetParts);

        // --- Construction des sources <picture> pour WebP (si disponibles) ---
        $sourcesHtml = '';
        $webpSrcsetParts = [];
        if (isset($formats['thumbnail_webp'])) {
            $webpSrcsetParts[] = $this->assetPackages->getUrl($formats['thumbnail_webp'], 'uploads_images') . ' 150w';
        }
        if (isset($formats['medium_webp'])) {
            $webpSrcsetParts[] = $this->assetPackages->getUrl($formats['medium_webp'], 'uploads_images') . ' 600w';
        }
        if (isset($formats['large_webp'])) {
            $webpSrcsetParts[] = $this->assetPackages->getUrl($formats['large_webp'], 'uploads_images') . ' 1200w';
        }
        if (!empty($webpSrcsetParts)) {
            $sourcesHtml .= '<source srcset="' . implode(', ', $webpSrcsetParts) . '" type="image/webp" sizes="(max-width: 600px) 100vw, (max-width: 1200px) 50vw, 800px">';
        }

        // --- Génération des attributs supplémentaires ---
      /*  $extraAttributes = '';
        foreach ($attributes as $attrName => $attrValue) {
            // Évitez de dupliquer ou d'écraser des attributs clés comme src, srcset, sizes, alt
            if (!in_array($attrName, ['src', 'srcset', 'sizes', 'alt', 'loading'])) {
                $extraAttributes .= sprintf(' %s="%s"', htmlspecialchars($attrName), htmlspecialchars($attrValue));
            }
        }*/

        // Default sizes attribute (adjust based on your CSS/layout)
        $sizes = '(max-width: 600px) 100vw, (max-width: 1200px) 50vw, 800px';

        // --- Construction de la balise <picture> ou <img> finale ---
        $html = '';
        if ($sourcesHtml) {
            $html .= '<picture>';
            $html .= $sourcesHtml;
        }

        $html .= sprintf(
            '<img src="%s" srcset="%s" sizes="%s" alt="%s"%s loading="lazy">',
            htmlspecialchars($finalSrc),
            htmlspecialchars($srcset),
            htmlspecialchars($sizes),
            htmlspecialchars($imageAltText),
            $extraAttributes
        );

        if ($sourcesHtml) {
            $html .= '</picture>';
        }

        return $html;
    }

    private function buildAttributes(array $attributes): string
    {
        //valeur par default
        $extraAttributes='';

        foreach ($attributes as $attrName => $attrValue) {
            // Évitez de dupliquer ou d'écraser des attributs clés comme src, srcset, sizes, alt
            if (!in_array($attrName, ['src', 'srcset', 'sizes', 'alt', 'loading'])) {
                $extraAttributes .= sprintf(' %s="%s"', htmlspecialchars($attrName), htmlspecialchars($attrValue));
            }
        }

        return $extraAttributes;
    }

    public function getImageUrlByFormat($srcFormat, $baseFileName, $formats){
        
         $finalSrc = match ($srcFormat) {
            'thumbnail' => $this->assetPackages->getUrl($formats['thumbnail'] ?? $baseFileName, 'uploads_images'),

            'medium'    => $this->assetPackages->getUrl($formats['medium'] ?? $baseFileName, 'uploads_images'),

            'small' => $this->assetPackages->getUrl($formats['small'] ?? $baseFileName, 'uploads_images'),

            'large'     => $this->assetPackages->getUrl($formats['large'] ?? $baseFileName, 'uploads_images'),

            'original_compressed' => $this->assetPackages->getUrl($formats['original_compressed'] ?? $baseFileName, 'uploads_images'),

            'large_webp' => $this->assetPackages->getUrl($formats['large_webp'] ?? $baseFileName, 'uploads_images'),

            'medium_webp' => $this->assetPackages->getUrl($formats['medium_webp'] ?? $baseFileName, 'uploads_images'),

            'small_webp' => $this->assetPackages->getUrl($formats['small_webp'] ?? $baseFileName, 'uploads_images'),

            'thumbnail_webp' => $this->assetPackages->getUrl($formats['thumbnail_webp'] ?? $baseFileName, 'uploads_images'),

            'original'  => $this->assetPackages->getUrl($baseFileName, 'uploads_images'),

             default  => $this->assetPackages->getUrl($baseFileName, 'uploads_images'),
            };
       
        return $finalSrc;
    }

  


}