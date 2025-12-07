<?php

namespace App\Controller\Admin;

// Importations nécessaires pour l'entité Post
use App\Entity\Post;
// Importation nécessaire pour l'entité SeoMetadata (car nous l'utilisons)
use App\Entity\SeoMetadata;

// Importations des classes de base et des types de champs d'EasyAdmin
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;          // Pour les champs texte simples (slug, postType, ogTitle, ogImage)
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;    // Pour les champs texte longs (description, ogDescription, schemaJson)
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;       // Pour le champ entityId
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;          // Pour créer des sections (panneaux) dans le formulaire
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField; // Pour 
// Importations pour la gestion manuelle de la persistance (persistEntity, updateEntity)
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext; // Nécessaire pour getContext()->getRequest()
use App\Enum\FollowGoogle;
use App\Enum\IndexGoogle;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PostCrudController extends AbstractCrudController
{
    /**
     * Méthode obligatoire : Indique à EasyAdmin quelle entité ce contrôleur gère.
     * C'est le "Fully Qualified Class Name" de l'entité Post.
     */
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    /**
     * Définit les champs qui seront affichés dans la liste des Posts et dans le formulaire
     * de création/édition.
     *
     * @param string $pageName Le nom de la page actuelle (index, new, edit, detail)
     * @return iterable Une collection de définitions de champs
     */
    public function configureFields(string $pageName): iterable
    {
        // --- SECTION PRINCIPALE : Champs de l'entité Post ---
        // Ce sont les propriétés directes de votre entité App\Entity\Post

        // Champ 'slug' : le segment d'URL unique pour le post
        yield TextField::new('slug')
            ->setLabel('Slug (URL)') // Libellé affiché dans l'admin
            ->setHelp('Partie de l\'URL unique pour ce post (ex: mon-super-article)'); // Texte d'aide

        // Champ 'postType' : le type de post (ex: article, page_statique, produit)
        yield TextField::new('postType')
            ->setLabel('Type de Contenu')
            ->setHelp('Type de contenu associé à ce post (ex: article, static_page)');

        // Champ 'entityId' : ID de l'entité réelle si c'est un post dynamique
        yield IntegerField::new('entityId')
            ->setLabel('ID Entité Liée')
            ->setRequired(false) // Non obligatoire (ex: pour les pages statiques)
            ->setHelp('ID de l\'article, produit, etc., associé. Laisser vide pour les pages statiques.');

        // --- SECTION "SEO Métadonnées" : Champs de l'entité SeoMetadata embarqués ---
        // Nous utilisons FormField::addPanel pour créer une section visuelle distincte dans le formulaire.
        yield FormField::addPanel('SEO Métadonnées')->setIcon('fas fa-globe'); // Ajout d'une icône pour le panneau

        // Ces champs pointent vers des propriétés de l'objet 'seoMetadata' lié au 'Post'
        // EasyAdmin essaiera d'appeler $post->getSeoMetadata()->getTitle() pour récupérer la valeur.

        yield TextField::new('seoMetadata.title')
            ->setLabel('Titre SEO')
            ->setRequired(false) // Le champ n'est pas obligatoire
            ->setHelp('Titre de la page pour les moteurs de recherche et les onglets du navigateur (&lt;title&gt;) .');

        yield TextareaField::new('seoMetadata.description')
            ->setLabel('Méta-description')
            ->setRequired(false)
            ->setHelp('Description courte pour les moteurs de recherche (<meta name="description">).');

        yield TextField::new('seoMetadata.ogTitle')
            ->setLabel('OG Title')
            ->setRequired(false)
            ->setHelp('Titre pour le partage sur les réseaux sociaux (Open Graph).');

        yield TextareaField::new('seoMetadata.ogDescription')
            ->setLabel('OG Description')
            ->setRequired(false)
            ->setHelp('Description pour le partage sur les réseaux sociaux (Open Graph).');

        yield TextField::new('seoMetadata.ogImage')
            ->setLabel('OG Image URL')
            ->setRequired(false)
            ->setHelp('URL de l\'image à afficher lors du partage sur les réseaux sociaux.');

        yield ChoiceField::new('seoMetadata.followGoogle')
        ->setFormType(ChoiceType::class)
        ->setChoices([
            'Suivre' => FollowGoogle::FOLLOW,
            'Ne pas suivre' => FollowGoogle::NOFOLLOW,            
        ])
        ->setFormTypeOption('choice_value', fn (?FollowGoogle $e) => $e?->value)
        ->renderExpanded(false)
        ->setRequired(false);

        yield ChoiceField::new('seoMetadata.indexGoogle')
        ->setFormType(ChoiceType::class)
        ->setChoices([
            'Indexer' => IndexGoogle::INDEX,
            'Ne pas indexer' => IndexGoogle::NOINDEX,
            ])
        ->setFormTypeOption('choice_value', fn (?IndexGoogle $e) => $e?->value)
        ->renderExpanded(false)
        ->setRequired(false);
        

        yield TextareaField::new('seoMetadata.schemaJson')
            ->setLabel('Schema JSON')
            ->setRequired(false)
            ->setHelp('Données structurées au format JSON-LD pour les moteurs de recherche (ex: {"@context": "http://schema.org/", "@type": "Article"}).');
    }

    /**
     * Surcharge la méthode par défaut d'EasyAdmin pour la création d'une entité.
     * C'est ici que nous nous assurons que l'objet SeoMetadata est créé et lié au Post
     * si le Post est nouveau et qu'il n'a pas encore de SeoMetadata.
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entity): void
    {
        // On s'assure que l'entité est bien une instance de Post
        if ($entity instanceof Post) {
            $seoMetadata = $entity->getSeoMetadata(); // Récupère l'objet SeoMetadata lié
            
            // Si le Post n'a pas encore de SeoMetadata lié (c'est un nouveau Post ou un Post ancien sans SEO)
            if ($seoMetadata === null) {
                $seoMetadata = new SeoMetadata(); // Crée une nouvelle instance de SeoMetadata
                $entity->setSeoMetadata($seoMetadata); // Lie ce nouveau SeoMetadata au Post
            }
            
            // Assure que le SeoMetadata a une référence correcte vers son Post parent
            // (très important car SeoMetadata est le côté "owning" de la relation)
            $seoMetadata->setPost($entity);

            // Si ce SeoMetadata est un nouvel objet (n'a pas encore d'ID en BDD), on le persiste explicitement
            // La cascade 'persist' dans l'entité SeoMetadata pourrait le faire automatiquement,
            // mais c'est une sécurité supplémentaire et clarifie l'intention.
            if ($seoMetadata->getId() === null) {
                $entityManager->persist($seoMetadata);
            }
        }
        // Appelle la méthode parente pour persister l'entité Post elle-même.
        parent::persistEntity($entityManager, $entity);
    }

    public function createEntity(string $entityFqcn)
        {
            // Appelle la méthode parente pour instancier l'entité (donc un nouveau Post)
            $post = parent::createEntity($entityFqcn);

            // Crée une nouvelle instance de SeoMetadata et la lie au Post
            $seoMetadata = new SeoMetadata();
            $post->setSeoMetadata($seoMetadata);

            // Retourne l'entité Post avec son SeoMetadata déjà initialisé
            return $post;
        }


    /**
     * Surcharge la méthode par défaut d'EasyAdmin pour la mise à jour d'une entité existante.
     * Similaire à persistEntity, mais gère le cas où un SeoMetadata pourrait être ajouté à un Post existant.
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entity): void
    {
        // On s'assure que l'entité est bien une instance de Post
        if ($entity instanceof Post) {
            $seoMetadata = $entity->getSeoMetadata(); // Récupère l'objet SeoMetadata lié

            // Si le Post n'a pas de SeoMetadata (par ex. un ancien Post),
            // ET que des données SEO ont été soumises via le formulaire
            // (on vérifie la présence du champ 'title' pour estimer si des données SEO ont été saisies)
            if ($seoMetadata === null && $this->getContext()->getRequest()->request->has('Post[seoMetadata][title]')) {
                $seoMetadata = new SeoMetadata(); // Crée une nouvelle instance de SeoMetadata
                $entity->setSeoMetadata($seoMetadata); // Lie ce nouveau SeoMetadata au Post
                $seoMetadata->setPost($entity); // Assure la référence Post -> SeoMetadata
                $entityManager->persist($seoMetadata); // Persiste le nouvel objet SeoMetadata
            }
            // Si SeoMetadata existe (qu'il soit nouveau ou ancien), assure que la référence Post est à jour.
            // (utile si le Post a été détaché puis ré-attaché ou si le lien est recréé)
            elseif ($seoMetadata !== null) {
                $seoMetadata->setPost($entity);
            }
        }
        // Appelle la méthode parente pour mettre à jour l'entité Post elle-même.
        parent::updateEntity($entityManager, $entity);
    }
}