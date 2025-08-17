<?php

namespace App\Controller;

use App\Entity\Article; 
use App\Repository\ArticleRepository;
use App\Entity\Post; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleController extends AbstractController
{

     use SeoAwareControllerTrait;


    #[Route('/article/{slug}', name: 'app_article_show')]
    public function show(string $slug, EntityManagerInterface $entityManager): Response
    {
       
        // 1. D'abord, on récupère l'entité Article elle-même par son slug
        $article = $entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('L\'article demandé n\'existe pas.');
        }

        // 2. Ensuite, on récupère l'entité Post (le wrapper SEO) liée à cet Article.
        // On la trouve par son entityId (qui est l'ID de l'Article) et son postType.
      /*  $seoPost = $entityManager->getRepository(Post::class)->findOneBy([
            'entityId' => $article->getId(), // L'ID de l'article est l'entityId du Post
            'postType' => 'article',         // S'assurer que c'est le Post de type 'article'
        ]);*/

         $seoCriteria = [
            'postType' => 'article',        // Critère spécifique pour les articles
            'entityId' => $article->getId(), // L'ID de l'article est la clé
        ];

        // 3. >>> ON PASSE CE TABLEAU $seoCriteria AU TRAIT <<<
        $seoPost = $this->getSeoPostFromCriteria(
            $entityManager,  // L'EntityManager est aussi passé en paramètre
            $seoCriteria     // Ce tableau est maintenant la valeur du paramètre $criteria dans la méthode du trait
        );

        // Note : Si $seoPost est null ici, cela signifie qu'un Article existe mais n'a pas
        // de métadonnées SEO associées via un Post. Le template devra gérer ce cas.

        return $this->render('article/show.html.twig', [
            'article' => $article,      // L'entité Article (pour le contenu et le titre principal)
            'seoPost' => $seoPost,      // L'entité Post (pour les métadonnées SEO)
        ]);
    }

    #[Route('/article', name: 'app_article_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // 1. Récupérer tous les articles depuis la base de données
        // On utilise le repository de l'entité Article
        // On peut ajouter un tri, par exemple par date de création descendante
        $articles = $entityManager->getRepository(Article::class)->findBy([], ['publishedAt' => 'DESC']); 
        // Si vous n'avez pas de champ 'createdAt' dans Article, retirez le tableau de tri.
        // Ou triez par 'id' : findBy([], ['id' => 'DESC']);

        // 2. Rendre une vue Twig en lui passant la liste des articles
        return $this->render('article/index.html.twig', [
            'articles' => $articles, // Une collection d'objets Article
        ]);
    }
}
