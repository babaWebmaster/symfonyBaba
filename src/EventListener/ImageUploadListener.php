<?php

namespace App\EventListener;

use App\Entity\Image;
use App\Service\ImageProcessor;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile; // N'oubliez pas cet import !
use Vich\UploaderBundle\Mapping\PropertyMapping; // N'oubliez pas cet import !

// Si vous utilisez le VarDumper pour le débogage temporaire
// use Symfony\Component\VarDumper\VarDumper;

class ImageUploadListener implements EventSubscriberInterface
{
    private ImageProcessor $imageProcessor;
    private string $uploadDestination; // Maintenu car votre constructeur le demande

    public function __construct(ImageProcessor $imageProcessor, string $uploadDestination)
    {
        $this->imageProcessor = $imageProcessor;
        $this->uploadDestination = $uploadDestination;
    }

    /**
     * Indique à Symfony quels événements cet écouteur doit écouter.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            // Écoute l'événement AVANT l'upload pour récupérer le nom original du fichier client
            Events::PRE_UPLOAD => 'onVichPreUpload',
            // Écoute l'événement APRÈS l'upload pour effectuer le traitement des images
            Events::POST_UPLOAD => 'onVichPostUpload',
            // Supprimez toute autre référence à 'onPostUpload' ici, si elle existait.
        ];
    }

    /**
     * Méthode appelée par Vich AVANT le déplacement du fichier.
     * Idéal pour récupérer le nom original de l'UploadedFile avant qu'il ne soit potentiellement vidé.
     */
    public function onVichPreUpload(Event $event): void
    {
        // Si vous déboguez, décommentez et ajoutez des dump ici :
        // VarDumper::dump('--- onVichPreUpload déclenché ---');

        $object = $event->getObject();
        /** @var PropertyMapping $mapping */
        $mapping = $event->getMapping();

        // Vérifie si l'entité est une instance de votre classe Image
        // et si le champ qui a déclenché l'upload est bien 'imageFile' (nom de la propriété dans l'entité).
        if (!$object instanceof Image || $mapping->getFilePropertyName() !== 'imageFile') {
            // VarDumper::dump('Conditions d\'entité ou de mapping non remplies pour PRE_UPLOAD.');
            return;
        }

        /** @var UploadedFile|File|null $file */
        $file = $object->getImageFile();

        // Si c'est un fichier fraîchement uploadé (UploadedFile) et que le nom original n'est pas déjà défini
        if ($file instanceof UploadedFile && $object->getOriginalName() === null) {
            $object->setOriginalName($file->getClientOriginalName());
            // VarDumper::dump('Nom original défini sur l\'entité : ' . $object->getOriginalName());
        }
    }

    /**
     * Méthode appelée par Vich APRÈS le déplacement du fichier.
     * C'est ici que l'on traite l'image (redimensionnement, formats, etc.).
     */
    public function onVichPostUpload(Event $event): void
    {
        // Si vous déboguez, décommentez et ajoutez des dump ici :
        // VarDumper::dump('--- onVichPostUpload déclenché ---');

        $object = $event->getObject();
        /** @var PropertyMapping $mapping */
        $mapping = $event->getMapping();

        if (!$object instanceof Image || $mapping->getFilePropertyName() !== 'imageFile') {
            // VarDumper::dump('Conditions d\'entité ou de mapping non remplies pour POST_UPLOAD.');
            return;
        }

        $uploadedFileName = $object->getFileName(); // Le nom unique que Vich a donné au fichier
        if (!$uploadedFileName) {
            // VarDumper::dump('Nom de fichier généré par Vich manquant.');
            return;
        }

        // IMPORTANT : Utiliser le chemin d'upload DEPUIS ImageProcessor
        // car c'est lui qui est le responsable de l'emplacement des fichiers.
        $filePath = $this->imageProcessor->getUploadDir() . '/' . $uploadedFileName;

        // Assurez-vous que le fichier existe avant de tenter de l'ouvrir
        if (!file_exists($filePath)) {
            // error_log("Le fichier original uploadé par Vich n'a pas été trouvé à : " . $filePath);
            return;
        }

        // Crée un objet File à partir du chemin physique du fichier pour le passer à ImageProcessor
        $file = new File($filePath);

        // Appelle le service ImageProcessor pour générer les formats
        $generatedFormats = $this->imageProcessor->processImage($file, $uploadedFileName);

        // Met à jour la propriété 'formats' de l'entité
        $object->setFormats($generatedFormats);

        // Note : le nom original est censé être déjà défini par onVichPreUpload.
        // Pas besoin de le redéfinir ici.
    }
}