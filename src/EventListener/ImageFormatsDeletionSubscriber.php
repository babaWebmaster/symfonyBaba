<?php

namespace App\EventListener;

use App\Entity\Image; 
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events; 
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Filesystem\Filesystem;

class ImageFormatsDeletionSubscriber implements EventSubscriberInterface
{
    private Filesystem $filesystem;
    private string $uploadDir;

    public function __construct(Filesystem $filesystem, string $uploadDir)
    {
        $this->filesystem = $filesystem;
        $this->uploadDir = $uploadDir;
    }

    public function getSubscribedEvents(): array
    {
        return [
            // Ici, Events::preRemove fait référence à Doctrine\ORM\Events::preRemove
            Events::preRemove => 'preRemove',
        ];
    }

    public function preRemove(LifecycleEventArgs $args): void
    { 
        $entity = $args->getObject();

        if (!$entity instanceof Image) {
            return;
        }

        $fileName = $entity->getFileName();

        if ($fileName) {
            $baseFileName = pathinfo($fileName, PATHINFO_FILENAME);
            $imageSpecificDirectory = sprintf('%s/%s', $this->uploadDir, $baseFileName);

            if ($this->filesystem->exists($imageSpecificDirectory)) {
                try {
                    $this->filesystem->remove($imageSpecificDirectory);
                } catch (\Exception $e) {
                    error_log("Erreur lors de la suppression du dossier d'image " . $imageSpecificDirectory . " : " . $e->getMessage());
                }
            }
        }
    }
}