<?php

namespace App\EventSubscriber;

use App\Entity\Image;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent; // Gardons-le pour l'exemple si vous voulez, mais il n'est pas le cœur de votre besoin d'upload
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent; // NOUVEAU !
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent; 
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;  // NOUVEAU !
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile; // Pour le type de fichier

class EasyAdminImageUploadSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
           // AfterCrudActionEvent::class => ['onBeforeCrudAction', 10],
           // BeforeCrudActionEvent::class => ['onBeforeCrudAction', 10], // Optionnel, gardez si vous voulez voir les actions générales
          //  BeforeEntityPersistedEvent::class => ['onBeforeEntityPersisted', 10],
           // BeforeEntityUpdatedEvent::class => ['onBeforeEntityUpdated', 10],
        ];
    }

    // Méthode pour BeforeCrudActionEvent (si vous la gardez)
    public function onBeforeCrudAction(BeforeCrudActionEvent $event): void
    {
          /** @var AdminContext $adminContext */
        $adminContext = $event->getAdminContext(); // Ceci fonctionne, vous l'avez confirmé

        // <<< CORRECTION ici : on va dumper tout le contexte pour voir les méthodes disponibles >>>
        // On ne va pas chercher getEntityFqcn() tout de suite.
        // Si vous êtes sur une page "edit" ou "detail", $adminContext->getEntity() pourrait contenir l'entité.
        // Sur une page "new", ce serait null.
        dd(
            'DEBUG: EasyAdminImageUploadSubscriber - BeforeCrudActionEvent déclenché !',
            'Contexte Admin (objet complet) :', $adminContext
        );
    }


     public function onBeforeEntityPersisted(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();
        if (!$entity instanceof Image) { return; }
        dd( // <<< CE DD DOIT ÊTRE ACTIF !
            'DEBUG: BeforeEntityPersistedEvent déclenché (CRÉATION) !',
            'Entité:', $entity,
            'Fichier Uploadé:', $entity->getImageFile(), // TRÈS IMPORTANT : QUE CONTIENT CELA ?
            'Type du fichier:', get_debug_type($entity->getImageFile())
        );
    }

    public function onBeforeEntityUpdated(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntity();
        if (!$entity instanceof Image) { return; }
        dd( // <<< CE DD DOIT ÊTRE ACTIF !
            'DEBUG: BeforeEntityUpdatedEvent déclenché (MODIFICATION) !',
            'Entité:', $entity,
            'Fichier Uploadé:', $entity->getImageFile(), // TRÈS IMPORTANT : QUE CONTIENT CELA ?
            'Type du fichier:', get_debug_type($entity->getImageFile())
        );
    }
}