<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field; 
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField; // Pour le champ d'upload
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;   // Pour le texte alternatif et les noms
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;  // Pour afficher les formats (en lecture seule)
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;       // Pour la configuration du CRUD
use Vich\UploaderBundle\Form\Type\VichImageType;
use Doctrine\ORM\EntityManagerInterface; 
use App\EventListener\ImageFormatsDeletionSubscriber;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;

class ImageCrudController extends AbstractCrudController
{ 

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ImageFormatsDeletionSubscriber $imageFormatsDeletionSubscriber
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Image::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Image')
            ->setEntityLabelInPlural('Images')
            ->setPageTitle('index', 'Gestion des Images')
            ->setPageTitle('new', 'Ajouter une Image')
            ->setPageTitle('edit', 'Modifier l\'Image')
            ->setSearchFields(['originalName', 'fileName']);
    }

    public function configureFields(string $pageName): iterable
    {

    yield IdField::new('id')->hideOnForm();
        
         // Champ pour l'upload du fichier
    yield Field::new('imageFile') // 'imageFile' est la propriété de l'entité qui reçoit l'objet UploadedFile
        ->setLabel('Fichier Image')
        ->setFormType(VichImageType::class) // <<< Utilisez le type de formulaire de Vich !
        ->setFormTypeOptions([
            // 'attr' => ['accept' => 'image/*'], // C'est déjà la valeur par défaut pour VichImageType si pas de conf spécifique
            'required' => $pageName === Crud::PAGE_NEW,
             // Gère si le champ est obligatoire
            'allow_delete' => false,
            // Autres options spécifiques à VichImageType si besoin, par ex: 'download_link' => true
        ])
        // Les options setBasePath, setUploadDir, setUploadedFileNamePattern ne sont plus nécessaires ici pour le champ d'upload
        // car VichImageType utilise votre vich_uploader.yaml
        ->setHelp('Uploadez une image. Des formats multiples seront générés automatiquement.')
        ->onlyOnForms();

    yield  ImageField::new('fileName') // <-- Utilisez 'fileName' ici
                ->setBasePath('/uploads/images') // <-- Chemin URL de base de vos images
                ->setLabel('Aperçu') // Libellé pour la liste/détail
                ->hideOnForm();
        
    // Pour afficher l'image existante sur les pages d'édition ou de détail (Optionnel mais recommandé):
     if (in_array($pageName, [Crud::PAGE_EDIT, Crud::PAGE_DETAIL])) {
        // 'fileName' est la propriété de l'entité où Vich stocke le nom du fichier après l'upload
        yield ImageField::new('fileName')
            ->setLabel('Aperçu Image Actuelle')
            ->setBasePath('/uploads/images/') // Chemin URL public pour afficher l'image
            ->setFormTypeOption('disabled', true) // Rendre non modifiable
            ->hideOnForm(Crud::PAGE_NEW); // Cache cet aperçu sur la page de création
    }

   

        // Champ pour le texte alternatif (alt)
        yield TextField::new('altText')
            ->setLabel('Texte Alternatif (Alt)')
            ->setHelp('Texte descriptif de l\'image pour l\'accessibilité et le SEO (ex: "Logo de l\'entreprise X").');

        // Champ pour le nom original du fichier (lecture seule dans le formulaire)
        yield TextField::new('originalName')
            ->setLabel('Nom Original du Fichier')
            ->setHelp('Nom du fichier tel qu\'il a été uploadé.')
            ->setFormTypeOption('disabled', true) // Désactive la modification dans le formulaire
            ->hideOnForm(); // Cache ce champ sur le formulaire d'édition/création, mais visible en liste

        // Champ pour le nom de fichier généré (lecture seule)
        yield TextField::new('fileName')
            ->setLabel('Nom du Fichier Stocké')
            ->setHelp('Nom du fichier généré et stocké sur le serveur.')
            ->setFormTypeOption('disabled', true)
            ->hideOnForm();

        // Champ pour les formats générés (lecture seule)
        yield ArrayField::new('formats')
            ->setLabel('Formats Générés')
            ->setHelp('Chemins des différentes versions de l\'image.')
            ->hideOnForm()// Cache ce champ sur le formulaire, visible uniquement en liste
            ->hideOnIndex();

   
    }

    

       public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // On vérifie que c'est bien une entité Image que l'on est en train de supprimer
        if (!$entityInstance instanceof Image) {
            // Si ce n'est pas une Image, on laisse EasyAdmin faire sa suppression habituelle.
            parent::deleteEntity($entityManager, $entityInstance);
            return;
        }

        // --- C'est LA LIGNE IMPORTANTE pour la suppression des formats ! ---
        // On appelle manuellement la méthode 'preRemove' de notre nettoyeur.
        // On lui donne l'entité et l'EntityManager, comme si Doctrine l'avait fait.
        $this->imageFormatsDeletionSubscriber->preRemove(new \Doctrine\Persistence\Event\LifecycleEventArgs($entityInstance, $entityManager));

        // Ensuite, on laisse EasyAdmin (et Doctrine) supprimer l'entité de la base de données.
        parent::deleteEntity($entityManager, $entityInstance);
    }

   public function batchDelete(AdminContext $context, BatchActionDto $batchActionDto): Response
    {
        // 1. Récupérer les IDs des entités à supprimer depuis l'objet BatchActionDto
        $entityIds = $batchActionDto->getEntityIds();

        // 2. Récupérer toutes les entités Image correspondantes AVANT qu'elles soient supprimées de la base de données.
        // On utilise l'EntityManager injecté dans le constructeur de ce contrôleur.
        $imagesToDelete = $this->entityManager->getRepository(Image::class)->findBy(['id' => $entityIds]);

        // 3. Pour chaque image, déclencher notre logique de suppression des fichiers associés
        foreach ($imagesToDelete as $image) {
            // Appelez la méthode preRemove de notre ImageFormatsDeletionSubscriber.
            // On simule l'événement Doctrine en lui passant l'entité et l'EntityManager.
            $this->imageFormatsDeletionSubscriber->preRemove(new \Doctrine\Persistence\Event\LifecycleEventArgs($image, $this->entityManager));
        }

        // 4. Laisser EasyAdmin gérer la suppression par lots des entités de la base de données.
        // On appelle la méthode parente avec les arguments qu'elle attend.
        // Le parent::batchDelete() se charge généralement de la redirection après l'opération.
        return parent::batchDelete($context, $batchActionDto);
    }

   public function configureActions(Actions $actions): Actions
    {
    return $actions
        ->add(Crud::PAGE_INDEX, Action::new('uploadMultiple', 'Upload en Paquets')
            ->linkToRoute('#')
            ->createAsGlobalAction() 
            ->setCssClass('btn btn-success')
        );
    }

    public function configureAssets(Assets $assets): Assets
    {
     return Assets::new()->addJsFile('js/admin/image-bulk-upload.js');
    }



     

}