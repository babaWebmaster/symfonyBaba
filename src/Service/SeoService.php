<?php
namespace App\Service;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;

class SeoService
{

    public function __construct(private EntityManagerInterface $entityManager)
    {

    }


    public function getSeoPostFromCriteria(array $criteria): ?post
    {
        return $this->entityManager->getRepository(Post::class)->findOneBy($criteria);
    }


}



?>