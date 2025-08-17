<?php

namespace App\Service;

use Imagine\Gd\Imagine; // N'oubliez pas l'import de Imagine.Gd.Imagine (ou Imagick si vous l'avez)
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem; // Pour créer les dossiers si besoin

class ImageProcessor
{
    private string $uploadDir;
    private Filesystem $filesystem;
    private Imagine $imagine;

    // Le constructeur reçoit le chemin du répertoire du projet grâce à la configuration de service
    public function __construct(string $projectDir, Imagine $imagine)
    {
        // Définit le répertoire où les images seront stockées (doit correspondre à vich_uploader.yaml)
        $this->uploadDir = $projectDir . '/public/uploads/images';
        $this->filesystem = new Filesystem();
        $this->imagine = $imagine;
    }

    /**
     * Traite une image uploadée pour générer différents formats et la compresser.
     *
     * @param File   $file     Le fichier temporaire uploadé.
     * @param string $fileName Le nom du fichier généré par VichUploader (ex: 'unique-hash.jpg').
     * @return array Un tableau associatif des chemins URL publics des formats générés.
     */
    public function processImage(File $file, string $fileName): array
    {
        
        $originalPath = $file->getRealPath(); // Chemin physique du fichier original temporaire
        $image = $this->imagine->open($originalPath); // Ouvre l'image avec Imagine

        $formats = []; // Tableau pour stocker les chemins des formats générés
        $quality = 75; // Qualité de compression pour JPEG et WebP (0-100)

        // Définition des tailles souhaitées pour les images
        $sizes = [
            'large' => 1200,    // Pour les grands écrans
            'medium' => 800,   // Taille standard pour le contenu
            'small' => 400,    // Pour les miniatures ou des images de petite taille
            'thumbnail' => 150, // Très petite miniature
        ];

        // Extrait le nom de base du fichier sans extension pour créer des sous-dossiers et des noms propres
        $info = pathinfo($fileName);
        $baseFileName = $info['filename']; // ex: 'unique-hash'

        // Chemin du sous-dossier pour cette image spécifique
        $imageSpecificDir = sprintf('%s/%s', $this->uploadDir, $baseFileName);

        // Assurez-vous que le répertoire existe
        if (!$this->filesystem->exists($imageSpecificDir)) {
            $this->filesystem->mkdir($imageSpecificDir);
        }

        // --- Génération des différents formats (JPG/PNG selon l'original) ---
        foreach ($sizes as $name => $width) {
            $outputFilePath = sprintf('%s/%s-%s.%s',
                $imageSpecificDir,
                $baseFileName,
                $name,
                $info['extension'] // Garde l'extension originale (jpg, png)
            );
            $resizedImage = $this->resizeImage($image, $width);
            $this->saveImage($resizedImage, $outputFilePath, $quality);

            // Stocke le chemin public (relatif à public/)
           $formats[$name] = str_replace($this->uploadDir,'', $outputFilePath);
        }

        // --- Génération de la version WebP (pour une meilleure performance) ---
        // On génère aussi des versions WebP pour les tailles clés si le format original n'était pas WebP
        // et qu'on souhaite offrir cette option pour <picture>
        $webpSizes = [
            'large_webp' => 1200,
            'medium_webp' => 800,
            'small_webp' => 400,
            'thumbnail_webp' => 150,
            'original_webp' => $image->getSize()->getWidth() // WebP de la taille originale
        ];

        foreach ($webpSizes as $name => $width) {
            // Re-ouvre l'image originale si nécessaire pour éviter des redimensionnements en cascade
            $currentImage = ($name === 'original_webp') ? $image : $this->resizeImage($this->imagine->open($originalPath), $width);

            $outputFilePath = sprintf('%s/%s-%s.webp',
                $imageSpecificDir,
                $baseFileName,
                $name
            );
            $this->saveImage($currentImage, $outputFilePath, $quality, 'webp');
            $formats[$name] = str_replace($this->uploadDir, '', $outputFilePath);
        }

        // Sauvegarde de l'original compressé (si vous ne voulez pas garder l'original brut)
        // Ou vous pouvez choisir de ne pas sauvegarder l'original du tout si toutes les tailles dérivées suffisent
        $originalCompressedPath = sprintf('%s/%s-original-compressed.%s',
            $imageSpecificDir,
            $baseFileName,
            $info['extension']
        );
        $this->saveImage($image, $originalCompressedPath, $quality);
        $formats['original_compressed'] = str_replace($this->uploadDir, '', $originalCompressedPath);

        return $formats;
    }

    /**
     * Redimensionne une image à la largeur spécifiée en conservant les proportions.
     */
    private function resizeImage(ImageInterface $image, int $width): ImageInterface
    {
        $originalWidth = $image->getSize()->getWidth();
        // Si l'image originale est déjà plus petite ou égale à la largeur demandée, pas besoin de redimensionner
        if ($originalWidth <= $width) {
            return $image->copy(); // Retourne une copie pour éviter de modifier l'originale si elle est réutilisée
        }

        // Redimensionne l'image en conservant les proportions
        return $image->resize($image->getSize()->widen($width));
    }

    /**
     * Sauvegarde l'image dans un format spécifié avec une qualité donnée.
     */
    private function saveImage(ImageInterface $image, string $filePath, int $quality, string $format = null): void
    {
        // Crée le dossier si inexistant (la propriété $filesystem le gère maintenant)
        $directory = dirname($filePath);
        if (!$this->filesystem->exists($directory)) {
            $this->filesystem->mkdir($directory, 0777, true);
        }

        // Détermine les options de sauvegarde en fonction du format
        $options = [];
        if ($format === 'webp') {
            $options['webp_quality'] = $quality;
            $options['format'] = 'webp';
        } elseif ($format === 'jpeg' || $format === 'jpg') { // Explicite pour JPG
            $options['jpeg_quality'] = $quality;
            $options['format'] = 'jpeg';
        } elseif ($format === 'png') { // Spécifique pour PNG (la qualité est différente)
             // La qualité PNG est inversée (0=pas de compression, 9=compression max)
            $options['png_compression_level'] = round(9 - ($quality / 100 * 9));
            $options['format'] = 'png';
        } else {
            // Pour les autres formats ou par défaut, on utilise la qualité JPEG/WebP
            // Imagine s'adapte à l'extension du fichier cible s'il n'y a pas de format défini
            $options['jpeg_quality'] = $quality;
            $options['webp_quality'] = $quality; // Au cas où l'extension est .webp
        }

        $image->save($filePath, $options);
    }

    public function getUploadDir(): string
    {
        return $this->uploadDir;
    }
}