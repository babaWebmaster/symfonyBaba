<?php
// src/Service/GetBackgroundFooter.php

namespace App\Service; // Assurez-vous d'avoir un namespace

use App\Service\ImageHelper;

// Le nom de la classe doit correspondre au nom du fichier
class GetBackgroundFooter
{
    public function __construct(private ImageHelper $imageHelper)
    {

    }

    public function getBackground(int $id): ?array
    {
        $imageData = $this->imageHelper->getImageDataForTemplate($id, [], 'thumbnail', false);

        // Vérifie si des données ont été trouvées
        if ($imageData && isset($imageData['image_object'])) {
            // Retourne directement l'objet Image
            return $imageData;
        }

        // Retourne null si aucune image n'est trouvée
        return null;
    }
}