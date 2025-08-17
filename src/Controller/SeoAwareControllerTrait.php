<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Trait SeoAwareControllerTrait
 * Fournit une méthode générique pour récupérer l'entité Post (le conteneur SEO)
 * en fonction des critères fournis par le contrôleur appelant.
 */
trait SeoAwareControllerTrait
{
    /**
     * Récupère l'entité Post (le conteneur des métadonnées SEO) en utilisant les critères de recherche fournis.
     * Le contrôleur appelant est responsable de construire le tableau de critères spécifique au type de contenu.
     *
     * @param EntityManagerInterface $entityManager L'EntityManager pour interagir avec la base de données.
     * @param array $criteria Un tableau de critères de recherche pour l'entité Post (ex: ['postType' => 'article', 'entityId' => 123]).
     * @return Post|null L'entité Post si trouvée, null sinon.
     */
    private function getSeoPostFromCriteria(EntityManagerInterface $entityManager, array $criteria): ?Post
    {     
        // La logique de recherche est simple : juste un findOneBy avec les critères donnés
        return $entityManager->getRepository(Post::class)->findOneBy($criteria);
    }
}