<?php

namespace App\Service;

use App\Service\ImageHelper;

class ImagePreviewBuilderMaquette
{
    public function __construct(private ImageHelper $imageHelper )
    {

    }

    public function buildUrlsFromIds(string $ids): array
    {
        $arrayIdImage = explode(',', $ids);
        $arrayUrlImage = [];

        foreach ($arrayIdImage as $id) {
            $format = 'large_webp';
            $image = $this->imageHelper->getImageDataForTemplate((int)$id, [], $format, true);

            if (!$image['found']) {
                $image = $this->imageHelper->getImageDataForTemplate((int)$id, [], 'large', true);
            }

            $arrayUrlImage[] = $image['html'];
        }

        return $arrayUrlImage;
    }


    public function buildUrlFromId(string $ids): string
    {
        $arrayIdImage = explode(',', $ids);
        $idImage = $arrayIdImage[0];
        $image = $this->imageHelper->getImageDataForTemplate($idImage,[],'small_webp',false);

        
        return $image['html'];

        
    }

}


?>